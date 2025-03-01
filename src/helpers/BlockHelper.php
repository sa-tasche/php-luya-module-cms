<?php

namespace luya\cms\helpers;

use luya\admin\helpers\Angular;
use luya\cms\LinkConverter;
use luya\TagParser;
use Yii;

/**
 * Helper methods for CMS Blocks.
 *
 * As before those options has been stored in the {{\luya\cms\base\InternalBaseBlock}} they
 * are now located as static helper methods here.
 *
 * The helper methods are basically tasks you are using a lot when dealing with block extra
 * value output or configuration of a block element like vars, cfgs.
 *
 * The general setup of using block helper is to assigne the value into extra vars from a given cfg
 * or var input:
 *
 * ```php
 * public function extraVars()
 * {
 *     return [
 *         'markdownText' => BlockHelper::markdown($this->getVarValue('text')),
 *     ];
 * }
 * ```
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class BlockHelper
{
    /**
     * Create option inputs for radio type.
     *
     * @param array $options
     * @return array
     */
    public static function radioArrayOption(array $options)
    {
        return Angular::optionsArrayInput($options);
    }

    /**
     * Create the options array for a zaa-select field based on an key value pairing
     * array.
     *
     * In order to provide a prompt message which displayes when nothing is selected us the $prompt param.
     *
     * In order to get a key value pairing directly from an ActiveRecord Model use:
     *
     * ```php
     * return Tag::find()->select(['name'])->indexBy('id')->column();
     * ```
     *
     * @param array $options The key value array pairing the select array should be created from.
     * @param string $prompt The prompt message when nothing is selected (contains the value 0 by default).
     * @return array
     * @since 1.0.0
     */
    public static function selectArrayOption(array $options, $prompt = null)
    {
        $array = Angular::optionsArrayInput($options);

        if ($prompt) {
            array_unshift($array, ['label' => $prompt, 'value' => 0]);
        }

        return $array;
    }

    /**
     * Create the Options list in the config for a zaa-checkbox-array based on an
     * key => value pairing array.
     *
     * In order to get a key value pairing directly from an ActiveRecord Model use:
     *
     * ```php
     * return Tag::find()->select(['name'])->indexBy('id')->column();
     * ```
     *
     * Where name is the value and id the key for the array.
     *
     * @param array $options The array who cares the options with items
     * @return array
     * @since 1.0.0
     */
    public static function checkboxArrayOption(array $options)
    {
        return ['items' => Angular::optionsArrayInput($options)];
    }

    /**
     * Get all informations from an zaa-image-upload type:
     *
     * ```php
     * 'image' => BlockHelper::ImageUpload($this->getVarValue('myImage')),
     * ```
     *
     * apply a filter for the image
     *
     * ```php
     * 'imageFiltered' => BlockHelper::ImageUpload($this->getVarValue('myImage'), 'small-thumbnail'),
     * ```
     *
     * @param string|int $value Provided the value
     * @param boolean|string $applyFilter To apply a filter insert the identifier of the filter.
     * @param boolean $returnObject Whether the storage object should be returned or an array.
     * @return boolean|array|luya\admin\image\Item Returns false when not found, returns an array with all data for the image on success.
     */
    public static function imageUpload($value, $applyFilter = false, $returnObject = false)
    {
        if (empty($value)) {
            return false;
        }

        $image = Yii::$app->storage->getImage($value);

        if (!$image) {
            return false;
        }

        if ($applyFilter && is_string($applyFilter)) {
            $filter = $image->applyFilter($applyFilter);

            if ($filter) {
                if ($returnObject) {
                    return $filter;
                }
                return $filter->toArray();
            }
        }

        if ($returnObject) {
            return $image;
        }
        return $image->toArray();
    }

    /**
     * Get the full array for the specific zaa-file-image-upload type
     *
     * ```php
     * 'imageList' => BlockHelper::ImageArrayUpload($this->getVarValue('images')),
     * ```
     *
     * Each array item will have all file query item data and a caption key.
     *
     * @param string|int $value The specific var or cfg fieldvalue.
     * @param boolean|string $applyFilter To apply a filter insert the identifier of the filter.
     * @param boolean $returnObject Whether the storage object should be returned or an array.
     * @return array Returns an array in any case, even an empty array.
     */
    public static function imageArrayUpload($value, $applyFilter = false, $returnObject = false)
    {
        if (!empty($value) && is_array($value)) {
            $data = [];

            foreach ($value as $key => $item) {
                $image = static::imageUpload($item['imageId'], $applyFilter, true);
                if ($image) {
                    if ($item['caption']) {
                        $image->caption = $item['caption'];
                    }

                    $data[$key] = ($returnObject) ? $image : $image->toArray();
                }
            }

            return $data;
        }

        return [];
    }

    /**
     * Get file information based on input fileId.
     *
     * In order to use the FileUpload helper, define an extraVar based on the fileId from the cfg or
     * var configurations.
     *
     * ```php
     * public function extraVars()
     * {
     *     return [
     *         'file' => BlockHelper::FileUpload($this->getVarValue('myFile'), true)
     *     ];
     * }
     * ```
     *
     * Attention: Always use if statement in your view file to check if file exists. An example view
     * for the above defined extra var `file`:
     *
     * ```html
     * <?php if ($this->extraValue('file')): ?>
     *      <a href="<?= $this->extraValue('file')->href; ?>">File Download</a>
     * <?php endif; ?>
     * ```
     *
     * @param integer $fileId The file id from a config or cfg value in order to find the file.
     * @param boolean $returnObject Whether the storage object should be returned or an array, if the file could not be found this parameter is
     * has no affect to the response and will return false.
     * @return boolean|array|\luya\admin\file\Item Returns an array or the {{\luya\admin\file\Item}} object if the file could be find, otherwise the response is false. Make
     * sure to check whether return value is false or not to ensure no exception will be thrown.
     */
    public static function fileUpload($fileId, $returnObject = false)
    {
        if (!empty($fileId)) {
            $file = Yii::$app->storage->getFile($fileId);
            /* @var \luya\admin\file\Item $file */
            if ($file) {
                if ($returnObject) {
                    return $file;
                }
                return $file->toArray();
            }
        }

        return false;
    }

    /**
     * Get the full array for the specific zaa-file-array-upload type
     *
     * ```php
     * 'fileList' => BlockHelper::FileArrayUpload($this->getVarValue('files')),
     * ```
     *
     * Each array item will have all file query item data and a caption key.
     *
     * @param string|int $value The specific var or cfg fieldvalue.
     * @param boolean $returnObject Whether the storage object should be returned or an array.
     * @return array Returns an array in any case, even an empty array.
     */
    public static function fileArrayUpload($value, $returnObject = false)
    {
        if (!empty($value) && is_array($value)) {
            $data = [];
            foreach ($value as $key => $item) {
                $file = static::fileUpload($item['fileId'], true);
                if ($file) {
                    if (!empty($item['caption'])) {
                        $file->caption = $item['caption'];
                    } else {
                        $file->caption = $file->name;
                    }
                    $data[$key] = ($returnObject) ? $file : $file->toArray();
                }
            }

            return $data;
        }

        return [];
    }


    /**
     * Generate a link object based on the configuration (array).
     *
     * @param array $config The configuration array to build the object the config array requires the following keys
     * + type: The type of redirect (1 = internal, 2 = external, 3 = file, etc.)
     * + value: The value assoiated to the type (link to a file can be an integer, redirect to an external page string with an url)
     * + target: (optional)
     * @return \luya\web\LinkInterface|false Returns a linkable resource object or false if configuration is wrong.
     */
    public static function linkObject($config)
    {
        if (empty($config) || !is_array($config)) {
            return false;
        }

        $converter = LinkConverter::fromArray($config);

        if (!$converter) {
            return false;
        }

        return $converter->getLink();
    }

    /**
     * Wrapper function for Markdown Parsing.
     *
     * An example of assigned a text variable into a parsed extra Variable:
     *
     * ```php
     * public function extraVars()
     * {
     *     return [
     *         'markdownText' => BlockHelper::markdown($this->getVarValue('text')),
     *     ];
     * }
     * ```
     *
     * @param string $text The text to parse with Markdown.
     * @return string The parsed Markdown text.
     */
    public static function markdown($text)
    {
        return TagParser::convertWithMarkdown($text);
    }
}
