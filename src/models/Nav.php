<?php

namespace luya\cms\models;

use luya\admin\models\Group;
use luya\admin\ngrest\base\NgRestActiveQuery;
use luya\admin\traits\TaggableTrait;
use luya\cms\admin\Module;
use luya\cms\models\Property as CmsProperty;
use luya\helpers\Json;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * CMS Nav Model ActiveRecord
 *
 * This is the main class for the cms navigation/menu structure. The Nav item contains information about the state of the page like visibility,
 * sort-index, online or offline. It also contains information about its a child of another nav element, but it does **NOT** contain informations
 * about the content, title or alias (link) itself, cause those informations are stored in the the [[\cmsadmin\models\NavItem]] to the corresponding
 * language. So basically the Nav contains the structure and state of the menu/navigation put not content, or titles cause those are related to a language.
 *
 * @property integer $id
 * @property integer $nav_container_id
 * @property integer $parent_nav_id
 * @property integer $sort_index
 * @property integer $is_deleted
 * @property integer $is_hidden
 * @property integer $is_offline
 * @property integer $is_home
 * @property integer $is_draft
 * @property string $layout_file
 *
 * @property NavContainer $navContainer Returns the nav container model
 * @property NavItem[] $navItems
 * @property Nav[] $parents
 * @property NavItem $activeLanguageItem Returns the NavItem for the current active user language with with the context object nav id.
 * @property NavItem $defaultLanguageItem Reutrns the NavItem for the admin default language.
 * @property CmsProperty[] $properties
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Nav extends ActiveRecord
{
    use TaggableTrait;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cms_nav';
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'eventBeforeInsert']);
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'reindex']);
        $this->on(self::EVENT_AFTER_UPDATE, [$this, 'reindex']);
        $this->on(self::EVENT_AFTER_DELETE, [$this, 'reindex']);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nav_container_id'], 'required'],
            [['is_hidden', 'is_offline', 'sort_index', 'is_deleted', 'is_home', 'is_draft', 'layout_file'], 'safe'],
            [['layout_file'], 'match', 'pattern' => '/^[a-zA-Z0-9\.\-\_]+$/'],
            [['publish_from', 'publish_till'], 'integer'], // will be removed in 5.0
            [['parent_nav_id'], 'integer'],
//            ['parent_nav_id', 'exist', 'targetRelation' => 'parents'],
            [['parent_nav_id'], 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function fields()
    {
        $fields = parent::fields();
        // ensure boolean values are returned as integer
        // this is required when working with pgsql
        $fields['is_hidden'] = fn ($model) => (int) $model->is_hidden;
        $fields['is_deleted'] = fn ($model) => (int) $model->is_deleted;
        $fields['is_draft'] = fn ($model) => (int) $model->is_draft;
        $fields['is_home'] = fn ($model) => (int) $model->is_home;
        $fields['is_offline'] = fn ($model) => (int) $model->is_offline;
        return $fields;
    }

    /**
     * Get the parent elements
     *
     * @return NgRestActiveQuery|ActiveQuery
     * @since 3.1.0
     */
    public function getParents()
    {
        return $this->hasOne(static::class, ['parent_nav_id' => 'id']);
    }

    /**
     * Get current active language item.
     *
     * Get the cms_nav_item for this nav object with the corresponding current active language id (based
     * on the composition component).
     *
     * @return NgRestActiveQuery|ActiveQuery The corresponding nav item object for the active language.
     */
    public function getActiveLanguageItem()
    {
        return $this->hasOne(NavItem::class, ['nav_id' => 'id'])->andWhere(['lang_id' => Yii::$app->adminLanguage->activeId]);
    }

    /**
     * Get default language item.
     *
     * Get the cms_nav_item for this nav object with the corresponding default admin language id.
     *
     * @return NgRestActiveQuery|ActiveQuery The corresponding nav item object for the active language.
     * @since 3.1.2
     */
    public function getDefaultLanguageItem()
    {
        return $this->hasOne(NavItem::class, ['nav_id' => 'id'])->andWhere(['lang_id' => Yii::$app->adminLanguage->defaultLanguage['id']]);
    }

    /**
     * Return all nav items related to this object.
     *
     * @return NgRestActiveQuery|ActiveQuery
     */
    public function getNavItems()
    {
        return $this->hasMany(NavItem::class, ['nav_id' => 'id']);
    }

    /**
     *
     * @return NgRestActiveQuery|ActiveQuery
     */
    public function getNavContainer()
    {
        return $this->hasOne(NavContainer::class, ['id' => 'nav_container_id']);
    }

    /**
     * @return
     */
    public function createCopy($asTempalte = false)
    {
        $model = new self();
        $model->attributes = $this->toArray();
        $model->is_hidden = true;
        $model->is_offline = true;
        $model->is_home = false;
        $model->is_draft = $asTempalte;
        if ($model->save(false)) {
            return $model;
        }
    }

    /**
     * CmsProperty Active Query.
     *
     * @return NgRestActiveQuery|ActiveQuery
     */
    public function getProperties()
    {
        return $this->hasMany(CmsProperty::class, ['nav_id' => 'id']);
    }

    /**
     *
     * @param string $varName
     * @return boolean|\luya\admin\base\Property
     */
    public function getProperty($varName)
    {
        foreach ($this->properties as $prop) {
            if ($prop->adminProperty->var_name == $varName) {
                return $prop->getObject();
            }
        }

        return false;
    }

    /**
     * See if a given group has perrmission for the current nav model.
     *
     * @param Group $group
     * @return boolean
     */
    public function hasGroupPermission(Group $group)
    {
        $definitions = (new Query())->select("nav_id")->from("cms_nav_permission")->where(['group_id' => $group->id])->all();

        // the group has no permission defined, this means he can access ALL cms pages
        if (count($definitions) == 0) {
            return true;
        }

        foreach ($definitions as $permission) {
            if ($this->id == $permission['nav_id']) {
                return true;
            }
        }

        return false;
    }

    /**
     * See if the given group has permission to the current nav model.
     *
     * @param Group $group
     * @return boolean
     */
    public function hasGroupPermissionSelected(Group $group)
    {
        $definition = (new Query())->select("inheritance")->from("cms_nav_permission")->where(['group_id' => $group->id, 'nav_id' => $this->id])->one();

        if ($definition) {
            return true;
        }

        return false;
    }

    /**
     * See if a given group has inherited permission to the current nav model.
     *
     * @param Group $group
     * @return boolean
     */
    public function isGroupPermissionInheritNode(Group $group)
    {
        $definition = (new Query())->select("inheritance")->from("cms_nav_permission")->where(['group_id' => $group->id, 'nav_id' => $this->id])->one();

        if ($definition) {
            return (bool) $definition['inheritance'];
        }

        return false;
    }

    /**
     * find the latest sort index cms_nav item for the current nav_container_id and parent_nav_id and set internal index count plus one.
     */
    public function eventBeforeInsert()
    {
        $item = self::find()->where([
            'nav_container_id' => $this->nav_container_id,
            'parent_nav_id' => $this->parent_nav_id
        ])->orderBy('sort_index DESC')->limit(1)->asArray()->one();
        if (!$item) {
            $this->sort_index = 1;
        } else {
            $this->sort_index = $item['sort_index'] + 1;
        }
    }

    /**
     * Re-Index the current pages.
     *
     * @param \yii\base\Event $e
     */
    public function reindex($e)
    {
        $i = 1;
        foreach (self::find()->where([
            'nav_container_id' => $this->nav_container_id,
            'parent_nav_id' => $this->parent_nav_id
        ])->orderBy('sort_index ASC')->asArray()->all() as $model) {
            Yii::$app->db->createCommand()->update(
                self::tableName(),
                ['sort_index' => $i],
                'id=:id',
                ['id' => $model['id']]
            )->execute();
            ++$i;
        }

        switch ($e->name) {
            case 'afterInsert':
                Log::addAfterSave(1, ['tableName' => 'cms_nav', 'action' => 'insert', 'row' => $this->id], $e);
                break;
            case 'afterUpdate':
                Log::addAfterSave(2, ['tableName' => 'cms_nav', 'action' => 'update', 'row' => $this->id], $e);
                break;
            case 'afterDelete':
                Log::add(3, ['tableName' => 'cms_nav', 'action' => 'delete', 'row' => $this->id], 'cms_nav', $this->id, $this->toArray());
                break;
        }
    }

    /**
     * Get an array of all childrens of the current item recursivly.
     *
     * This method is mainly to find all recursive children of a nav item when moving a page into a container
     * all childrens requires to update its container id as well, so this method contains the data of its children
     *
     * @return Nav[] An array where each entry is a Nav object
     */
    public function getRecursiveChildren()
    {
        $data = [];
        $this->internalGetRecursiveChildren($data, $this->id);
        return $data;
    }

    /**
     * Resolve the current parent children of a given parent nav id informsion
     *
     * @param array $array
     * @param integer $parentNavId
     */
    private function internalGetRecursiveChildren(&$array, $parentNavId)
    {
        foreach (self::find()->where(['parent_nav_id' => $parentNavId])->all() as $item) {
            $array[] = $item;
            $this->internalGetRecursiveChildren($array, $item->id);
        }
    }

    // static helpers to move and copie

    /**
     * Move a nav model to a container.
     *
     * @param integer $moveNavId
     * @param integer $toCatId
     * @return boolean
     */
    public static function moveToContainer($moveNavId, $toCatId)
    {
        $move = self::findOne($moveNavId);

        $move->nav_container_id = $toCatId;
        $move->parent_nav_id = 0;
        $move->update();

        foreach ($move->getRecursiveChildren() as $child) {
            $child->updateAttributes(['nav_container_id' => $toCatId]);
        }

        return true;
    }

    /**
     * Check for duplicate alias in same parent_nav_id context of targetNav, comparing with currentNav item.
     * Additional this checks for matching language contexts when comparing aliases.
     *
     * @param $currentNavId
     * @param $parentNavId
     * @return boolean|mixed returns `true` if no duplication has been found, otherwhise returns an array with the duplicated existing item.
     */
    public static function checkDuplicateAlias($currentNavId, $parentNavId)
    {
        $currentNavItems = NavItem::find()->where(['nav_id' => $currentNavId])->asArray()->all();
        foreach (self::find()->where(['parent_nav_id' => $parentNavId, 'is_deleted' => false])->andWhere(['<>', 'id', $currentNavId])->asArray()->all() as $item) {
            foreach ($currentNavItems as $currentNavItem) {
                $itemNavItem = NavItem::find()->asArray()->where(['nav_id' => $item['id'], 'lang_id' => $currentNavItem['lang_id']])->one();
                if ($itemNavItem && $currentNavItem['alias'] == $itemNavItem['alias']) {
                    return $itemNavItem;
                }
            }
        }
        return true;
    }

    /**
     * Move a nav model before another nav model.
     *
     * @param integer $moveNavId
     * @param integer $toBeforeNavId
     * @return boolean|mixed
     */
    public static function moveToBefore($moveNavId, $toBeforeNavId)
    {
        $move = self::findOne($moveNavId);
        $to = self::findOne($toBeforeNavId);

        if (!$move || !$to) {
            return false;
        }

        $response = self::checkDuplicateAlias($move->id, $to->parent_nav_id);

        if ($response !== true) {
            return $response;
        }

        $to->moveUpstairs();

        $move->nav_container_id = $to->nav_container_id;
        $move->parent_nav_id = $to->parent_nav_id;
        $move->sort_index = $to->sort_index;
        $move->update();

        foreach ($move->getRecursiveChildren() as $child) {
            $child->updateAttributes(['nav_container_id' => $to->nav_container_id]);
        }

        return true;
    }

    /**
     * Moves an element ($moveNavId) after another one ($toAfterNavId).
     *
     * @param integer $moveNavId
     * @param integer $toAfterNavId
     * @return boolean|mixed
     */
    public static function moveToAfter($moveNavId, $toAfterNavId)
    {
        $move = self::findOne($moveNavId);
        $to = self::findOne($toAfterNavId);

        if (!$move || !$to) {
            return false;
        }

        $response = self::checkDuplicateAlias($move->id, $to->parent_nav_id);

        if ($response !== true) {
            return $response;
        }

        $to->moveUpstairs(false);
        $move->nav_container_id = $to->nav_container_id;
        $move->parent_nav_id = $to->parent_nav_id;
        $move->sort_index = $to->sort_index + 1;
        $move->update();

        foreach ($move->getRecursiveChildren() as $child) {
            $child->updateAttributes(['nav_container_id' => $to->nav_container_id]);
        }

        return true;
    }

    /**
     * Move a nav model to a child.
     *
     * @param integer $moveNavId
     * @param integer $droppedOnItemId
     * @return boolean|mixed
     */
    public static function moveToChild($moveNavId, $droppedOnItemId)
    {
        $move = self::findOne($moveNavId);
        $on = self::findOne($droppedOnItemId);

        if (!$move || !$on) {
            return false;
        }

        $response = self::checkDuplicateAlias($move->id, $on->id);

        if ($response !== true) {
            return $response;
        }

        $move->nav_container_id = $on->nav_container_id;
        $move->parent_nav_id = $on->id;
        $move->update();

        foreach ($move->getRecursiveChildren() as $child) {
            $child->updateAttributes(['nav_container_id' => $on->nav_container_id]);
        }

        return true;
    }

    /**
     * Raise the sort_index value for all elements where the current sort index is larger or equal.
     *
     * id|sort_index
     * --------
     * 1|0
     * 2|1
     * 3|2
     * 4|3
     *
     * apply moveUpstairs() to id 2 would produce the following output:
     *
     * id|sort_index
     * 1|0
     * 2|2
     * 3|3
     * 4|4
     *
     * if you disabled $withCurrentElement the output would be:
     *
     * id|sort_index
     * 1|0
     * 2|1
     * 3|3
     * 4|4
     *
     * @param $withCurrentElement boolean Whether the current element should be moved along with the others or not.
     */
    public function moveUpstairs($withCurrentElement = true)
    {
        $operator = $withCurrentElement ? '>=' : '>';
        $startIndex = $withCurrentElement ? (int) $this->sort_index : (int) $this->sort_index + 1;
        foreach (self::find()->where('sort_index '.$operator.' :index', ['index' => (int) $this->sort_index])->andWhere(['nav_container_id' => $this->nav_container_id, 'parent_nav_id' => $this->parent_nav_id])->orderBy('sort_index ASC')->asArray()->all() as $item) {
            ++$startIndex;
            Yii::$app->db->createCommand()->update(self::tableName(), ['sort_index' => $startIndex], 'id=:id', ['id' => $item['id']])->execute();
        }
    }

    /**
     * Reduces the sort_index value for all elements where the current sort index is larger or equal.
     *
     * id|index
     * --------
     * 1|0
     * 2|1
     * 3|2
     * 4|3
     *
     * apply moveDownstairs() to id 2 would produce the following output:
     *
     * id|index
     * 1|0
     * 2|0
     * 3|1
     * 4|2
     *
     * > This method is therefore only usefull when you want to push the latest item upstairs.
     */
    public function moveDownstairs()
    {
        $startIndex = (int)$this->sort_index;
        foreach (self::find()->where('sort_index >= :index', ['index' => $startIndex])->andWhere(['nav_container_id' => $this->nav_container_id, 'parent_nav_id' => $this->parent_nav_id])->orderBy('sort_index ASC')->asArray()->all() as $item) {
            --$startIndex;
            Yii::$app->db->createCommand()->update(self::tableName(), ['sort_index' => $startIndex], 'id=:id', ['id' => $item['id']])->execute();
        }
    }

    /**
     * Create a new nav item with a specific language, title and alias based on a given nav item id.
     * All content of the source nav item will be copied dependent on the nav item type (page content, module link, redirect informations).
     *
     * @param integer $navItemId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @return boolean|array If an array is returned, the creation had an error, the array contains the messages.
     */
    public function createItemLanguageCopy($navItemId, $langId, $title, $alias)
    {
        $sourceNavItem = NavItem::findOne($navItemId);

        if (!$sourceNavItem) {
            return ['id' => ["Unable to find nav item id {$navItemId}"]];
        }

        if (NavItem::find()->where(['nav_id' => $sourceNavItem->nav_id, 'lang_id' => $langId])->exists()) {
            return ['lang_id' => ["A translation for the given page already exists."]];
        }

        $navItem = new NavItem();
        $navItem->attributes = $sourceNavItem->toArray();
        $navItem->title = $title;
        $navItem->alias = $alias;
        $navItem->lang_id = $langId;
        $navItem->setParentFromModel();

        if (!$navItem->save()) {
            return $navItem->getErrors();
        }

        // we have created the copy, but its seems like no version exists for the original to copy page,
        // so we can not copy any content, lets return true and skip copy process.
        if (empty($sourceNavItem->nav_item_type_id)) {
            return true;
        }

        return $sourceNavItem->copyTypeContent($navItem);
    }

    /**
     * Create a page from a from a draft.
     *
     * @param integer $parentNavId
     * @param integer $navContainerId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @param string $description
     * @param integer $fromDraftNavId
     * @param boolean $isDraft
     * @return boolean|array If an array is returned, the creation had an error, the array contains the messages.
     */
    public function createPageFromDraft($parentNavId, $navContainerId, $langId, $title, $alias, $description, $fromDraftNavId, $isDraft = false)
    {
        if (!$isDraft && empty($isDraft) && !is_numeric($isDraft)) {
            $isDraft = 0;
        }

        $errors = [];
        // nav
        $nav = $this;
        $nav->attributes = [
            'parent_nav_id' => $parentNavId,
            'nav_container_id' => $navContainerId,
            'is_hidden' => true,
            'is_offline' => true,
            'is_draft' => $isDraft
        ];
        // nav item
        $navItem = new NavItem();
        $navItem->parent_nav_id = $parentNavId;
        $navItem->attributes = [
            'lang_id' => $langId,
            'title' => $title,
            'alias' => $alias,
            'description' => $description,
            'nav_item_type' => NavItem::TYPE_PAGE
        ];

        if (!$nav->validate()) {
            $errors = ArrayHelper::merge($nav->getErrors(), $errors);
        }
        if (!$navItem->validate()) {
            $errors = ArrayHelper::merge($navItem->getErrors(), $errors);
        }

        if (empty($fromDraftNavId)) {
            $errors['from_draft_id'] = [Module::t('model_navitempage_empty_draft_id')];
        }

        if (!empty($errors)) {
            return $errors;
        }

        // get draft nav item data
        $draftNavItem = NavItem::findOne(['nav_id' => $fromDraftNavId]);

        $navItemPageId = $draftNavItem->type->id;
        $layoutId = $draftNavItem->type->layout_id;
        $pageBlocks = NavItemPageBlockItem::findAll(['nav_item_page_id' => $navItemPageId]);

        // proceed save process
        $nav->save();
        $navItemPage = new NavItemPage();
        $navItemPage->layout_id = $layoutId;
        $navItemPage->timestamp_create = time();
        $navItemPage->version_alias = Module::VERSION_INIT_LABEL;
        $navItemPage->create_user_id = Module::getAuthorUserId();
        $navItemPage->nav_item_id = 0;

        if (!$navItemPage->validate()) {
            return $navItemPage->getErrors();
        }

        $navItemPage->save();

        $idLink = [];
        foreach ($pageBlocks as $block) {
            $i = new NavItemPageBlockItem();
            $i->attributes = $block->toArray();
            $i->nav_item_page_id = $navItemPage->id;
            $i->insert();
            $idLink[$block->id] = $i->id;
        }

        // check if prev_id is used, check if id is in set - get new id and set new prev_ids in copied items
        $newPageBlocks = NavItemPageBlockItem::findAll(['nav_item_page_id' => $navItemPage->id]);
        foreach ($newPageBlocks as $block) {
            if ($block->prev_id && isset($idLink[$block->prev_id])) {
                $block->updateAttributes(['prev_id' => $idLink[$block->prev_id]]);
            }
        }

        $navItem->nav_id = $nav->id;
        $navItem->nav_item_type_id = $navItemPage->id;

        $navItem->save();

        $navItemPage->updateAttributes(['nav_item_id' => $navItem->id]);

        return true;
    }

    /**
     * Create a new page.
     *
     * @param integer $parentNavId
     * @param integer $navContainerId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @param integer $layoutId
     * @param string $description
     * @param string $isDraft
     * @return array|integer If an array is returned the validationed failed, the array contains the error messages. If sucess the nav ID is returned.
     */
    public function createPage($parentNavId, $navContainerId, $langId, $title, $alias, $layoutId, $description, $isDraft = false)
    {
        $_errors = [];

        $nav = $this;
        $navItem = new NavItem();
        $navItem->parent_nav_id = $parentNavId;
        $navItemPage = new NavItemPage();

        if (!$isDraft && empty($isDraft) && !is_numeric($isDraft)) {
            $isDraft = 0;
        }

        $nav->attributes = [
            'parent_nav_id' => $parentNavId,
            'nav_container_id' => $navContainerId,
            'is_hidden' => true,
            'is_offline' => true,
            'is_draft' => $isDraft
        ];

        $navItem->attributes = [
            'lang_id' => $langId,
            'title' => $title,
            'alias' => $alias,
            'description' => $description,
            'nav_item_type' => NavItem::TYPE_PAGE
        ];

        $navItemPage->attributes = ['nav_item_id' => null, 'layout_id' => $layoutId, 'create_user_id' => Module::getAuthorUserId(), 'timestamp_create' => time(), 'version_alias' => Module::VERSION_INIT_LABEL];

        if (!$nav->validate()) {
            $_errors = ArrayHelper::merge($nav->getErrors(), $_errors);
        }
        if (!$navItem->validate()) {
            $_errors = ArrayHelper::merge($navItem->getErrors(), $_errors);
        }
        if (!$navItemPage->validate()) {
            $_errors = ArrayHelper::merge($navItemPage->getErrors(), $_errors);
        }

        if (!empty($_errors)) {
            return $_errors;
        }

        $navItemPage->save(false); // as validation is done already
        $nav->save(false); // as validation is done already

        $navItem->nav_item_type_id = $navItemPage->id;
        $navItem->nav_id = $nav->id;
        $navItemId = $navItem->save(false); // as validation is done already

        $navItemPage->updateAttributes(['nav_item_id' => $navItem->id]);

        return $nav->id;
    }

    /**
     * Create a nav item for a given NavId.
     *
     * If an array occurs an array with all errors is returned, if success the NavItem id is returned.
     *
     * @param integer $navId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @param integer $layoutId
     * @param string $description
     * @return array|integer If an array is returned the validationed failed, the array contains the error messages. If sucess the navItem ID is returned.
     */
    public function createPageItem($navId, $langId, $title, $alias, $layoutId, $description)
    {
        $_errors = [];

        $navItem = new NavItem();
        $navItem->parent_nav_id = self::findOne($navId)->parent_nav_id;
        $navItemPage = new NavItemPage();

        $navItem->attributes = [
            'nav_id' => $navId,
            'lang_id' => $langId,
            'title' => $title,
            'alias' => $alias,
            'description' => $description,
            'nav_item_type' => NavItem::TYPE_PAGE,
        ];

        $navItemPage->attributes = [
            'nav_item_id' => 0,
            'layout_id' => $layoutId,
            'create_user_id' => Module::getAuthorUserId(),
            'timestamp_create' => time(),
            'version_alias' => Module::VERSION_INIT_LABEL,
        ];

        if (!$navItem->validate()) {
            $_errors = ArrayHelper::merge($navItem->getErrors(), $_errors);
        }
        if (!$navItemPage->validate()) {
            $_errors = ArrayHelper::merge($navItemPage->getErrors(), $_errors);
        }

        if (!empty($_errors)) {
            return $_errors;
        }

        $navItemPage->save(false); // as validation is done already

        $navItem->nav_item_type_id = $navItemPage->id;
        $navItem->save(false); // as validation is done already

        $navItemPage->updateAttributes(['nav_item_id' => $navItem->id]);

        return $navItem->id;
    }

    /**
     * Create a new Module Page.
     *
     * @param integer $parentNavId
     * @param integer $navContainerId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @param string $moduleName
     * @param string $description
     * @return array|integer If an array is returned the validationed failed, the array contains the error messages. If sucess the nav ID is returned.
     */
    public function createModule($parentNavId, $navContainerId, $langId, $title, $alias, $moduleName, $description, $controllerName, $actionName, array $actionParams)
    {
        $_errors = [];

        $nav = $this;
        $navItem = new NavItem();
        $navItem->parent_nav_id = $parentNavId;
        $navItemModule = new NavItemModule();

        $nav->attributes = [
            'parent_nav_id' => $parentNavId,
            'nav_container_id' => $navContainerId,
            'is_hidden' => true,
            'is_offline' => true,
        ];

        $navItem->attributes = [
            'lang_id' => $langId,
            'title' => $title,
            'alias' => $alias,
            'description' => $description,
            'nav_item_type' => NavItem::TYPE_MODULE
        ];

        $navItemModule->attributes = ['module_name' => $moduleName, 'controller_name' => $controllerName, 'action_name' => $actionName, 'action_params' => Json::encode($actionParams)];

        if (!$nav->validate()) {
            $_errors = ArrayHelper::merge($nav->getErrors(), $_errors);
        }
        if (!$navItem->validate()) {
            $_errors = ArrayHelper::merge($navItem->getErrors(), $_errors);
        }
        if (!$navItemModule->validate()) {
            $_errors = ArrayHelper::merge($navItemModule->getErrors(), $_errors);
        }

        if (!empty($_errors)) {
            return $_errors;
        }

        $navItemModule->save(false); // as validation is done already
        $nav->save(false); // as validation is done already

        $navItem->nav_item_type_id = $navItemModule->id;
        $navItem->nav_id = $nav->id;
        $navItemId = $navItem->save(false); // as validation is done already

        return $nav->id;
    }

    /**
     *
     * @param integer $parentNavId
     * @param integer $navContainerId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @param integer $redirectType The type of redirect (1 = page, 2 = URL, 3 = Link to File)
     * @param string $redirectTypeValue Depending on the type (1 = cms_nav.id, 2 = https://luya.io)
     * @param string $description
     * @param string $redirectTypeTarget redirect target (_blank, self) (since 2.0)
     * @return array|integer If an array is returned the validationed failed, the array contains the error messages. If sucess the nav ID is returned.
     */
    public function createRedirect($parentNavId, $navContainerId, $langId, $title, $alias, $redirectType, $redirectTypeValue, $description, $redirectTypeTarget)
    {
        $_errors = [];

        $nav = $this;
        $navItem = new NavItem();
        $navItem->parent_nav_id = $parentNavId;
        $navItemRedirect = new NavItemRedirect();

        $nav->attributes = [
            'parent_nav_id' => $parentNavId,
            'nav_container_id' => $navContainerId,
            'is_hidden' => true,
            'is_offline' => true,
        ];
        $navItem->attributes = [
            'lang_id' => $langId,
            'title' => $title,
            'alias' => $alias,
            'description' => $description,
            'nav_item_type' => NavItem::TYPE_REDIRECT,
        ];
        $navItemRedirect->attributes = ['type' => $redirectType, 'value' => $redirectTypeValue, 'target' => $redirectTypeTarget];

        if (!$nav->validate()) {
            $_errors = ArrayHelper::merge($nav->getErrors(), $_errors);
        }
        if (!$navItem->validate()) {
            $_errors = ArrayHelper::merge($navItem->getErrors(), $_errors);
        }
        if (!$navItemRedirect->validate()) {
            $_errors = ArrayHelper::merge($navItemRedirect->getErrors(), $_errors);
        }

        if (!empty($_errors)) {
            return $_errors;
        }

        $navItemRedirect->save(false); // as validation is done already
        $nav->save(false); // as validation is done already

        $navItem->nav_item_type_id = $navItemRedirect->id;
        $navItem->nav_id = $nav->id;
        $navItemId = $navItem->save(false); // as validation is done already

        return $nav->id;
    }

    /**
     * Create a module for a given Nav Model.
     *
     * @param integer $navId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @param string $moduleName
     * @param string $description
     * @return array|integer If an array is returned the validationed failed, the array contains the error messages. If sucess the navItem ID is returned.
     */
    public function createModuleItem($navId, $langId, $title, $alias, $moduleName, $description, $controllerName, $actionName, array $actionParams)
    {
        $_errors = [];

        $navItem = new NavItem();
        $navItem->parent_nav_id = self::findOne($navId)->parent_nav_id;
        $navItemModule = new NavItemModule();

        $navItem->attributes = [
            'nav_id' => $navId,
            'lang_id' => $langId,
            'title' => $title,
            'alias' => $alias,
            'description' => $description,
            'nav_item_type' => NavItem::TYPE_MODULE,
        ];
        $navItemModule->attributes = ['module_name' => $moduleName, 'controller_name' => $controllerName, 'action_name' => $actionName, 'action_params' => Json::encode($actionParams)];

        if (!$navItem->validate()) {
            $_errors = ArrayHelper::merge($navItem->getErrors(), $_errors);
        }
        if (!$navItemModule->validate()) {
            $_errors = ArrayHelper::merge($navItemModule->getErrors(), $_errors);
        }

        if (!empty($_errors)) {
            return $_errors;
        }

        $navItemModule->save(false); // as validation is done already

        $navItem->nav_item_type_id = $navItemModule->id;
        $navItemId = $navItem->save(false); // as validation is done already

        return $navItem->id;
    }

    /**
     * Create a redirecte for a given Nav Model.
     * @param integer $navId
     * @param integer $langId
     * @param string $title
     * @param string $alias
     * @param integer $redirectType The type of redirect (1 = page, 2 = URL, 3 = Link to File)
     * @param string $redirectTypeValue Depending on the type (1 = cms_nav.id, 2 = https://luya.io)
     * @param string $description
     * @param string $redirectTypeTarget redirect target (_blank, self) (since 2.0)
     * @return array|integer If an array is returned the validationed failed, the array contains the error messages. If sucess the navItem ID is returned.
     */
    public function createRedirectItem($navId, $langId, $title, $alias, $redirectType, $redirectTypeValue, $description, $redirectTypeTarget)
    {
        $_errors = [];

        $navItem = new NavItem();
        $navItem->parent_nav_id = self::findOne($navId)->parent_nav_id;
        $navItemRedirect = new NavItemRedirect();

        $navItem->attributes = [
            'nav_id' => $navId,
            'lang_id' => $langId,
            'title' => $title,
            'alias' => $alias,
            'description' => $description,
            'nav_item_type' => NavItem::TYPE_REDIRECT,
        ];
        $navItemRedirect->attributes = ['type' => $redirectType, 'value' => $redirectTypeValue, 'target' => $redirectTypeTarget];

        if (!$navItem->validate()) {
            $_errors = ArrayHelper::merge($navItem->getErrors(), $_errors);
        }
        if (!$navItemRedirect->validate()) {
            $_errors = ArrayHelper::merge($navItemRedirect->getErrors(), $_errors);
        }

        if (!empty($_errors)) {
            return $_errors;
        }

        $navItemRedirect->save(false); // as validation is done already

        $navItem->nav_item_type_id = $navItemRedirect->id;
        $navItemId = $navItem->save(false); // as validation is done already

        return $navItem->id;
    }
}
