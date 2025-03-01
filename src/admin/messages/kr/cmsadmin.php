<?php

return [
    'view_index_add_type' => '페이지 유형',
    'view_index_type_page' => '페이지',
    'view_index_type_module' => '모듈',
    'view_index_type_redirect' => '전송',
    'view_index_as_draft' => '템플릿으로',
    'view_index_as_draft_help' => '새 페이지를 템플릿으로 정의 하시겠습니까?',
    'view_index_no' => '아니요',
    'view_index_yes' => '네',
    'view_index_page_title' => '페이지 제목',
    'view_index_page_alias' => 'URL 경로 세그먼트',
    'view_index_page_meta_description' => '설명',
    'view_index_page_nav_container' => '탐색 컨테이너',
    'view_index_page_parent_page' => '기본 페이지',
    'view_index_page_success' => '페이지가 성공적으로 생성되었습니다.',
    'view_index_page_parent_root' => '루트 수준',
    'view_index_page_use_draft' => '템플릿을 사용하실 건가요?',
    'view_index_page_select_draft' => '템플릿을 선택하실 건가요?',
    'view_index_page_layout' => '레이아웃 선택',
    'view_index_page_btn_save' => '새로운 페이지 저장하기',
    'view_index_module_select' => '모듈 이름',
    'view_index_sidebar_new_page' => '페이지 만들기',
    'view_index_sidebar_drafts' => '템플릿 관리',
    'view_index_sidebar_move' => '실행',
    'view_update_drop_blocks' => '이곳에 컨텐츠 블록 놓기',
    'view_update_blockcontent' => '컨텐츠 차단',
    'view_update_configs' => '선택적 구성',
    'view_update_settings' => '설정',
    'view_update_btn_save' => '저장하기',
    'view_update_btn_cancel' => '중단하기',
    'view_update_btn_hide_help' => 'Hide help',
    'view_update_btn_show_help' => 'Show help',
    'view_update_holder_state_on' => '자리 표시 자 접기',
    'view_update_holder_state_off' => '자리 표시 자 펼치기',
    'view_update_is_draft_mode' => '초안모드에서 편집',
    'view_update_is_homepage' => '홈페이지',
    'view_update_properties_title' => '페이지 속성',
    'view_update_no_properties_exists' => '이 페이지에는 아직 등록된 속성이 없습니다.',
    'view_update_draft_no_lang_error' => '템플릿이 이 언어로 설정되어 있지 않습니다.',
    'view_update_no_translations' => '이 페이지는 아직 번역되지 않았습니다.',
    'view_update_page_is_module' => '이 페이지는 <strong> module </strong>입니다.',
    'view_update_page_is_redirect_internal' => '이 페이지는 <show-internal-redirection nav-id = "typeData.value"/>에 대한 <strong>external redirection </strong>입니다.',
    'view_update_page_is_redirect_external' => '이 페이지는 <strong>external redirection</strong> to <a ng-href="{{typeData.value}}">{{typeData.value}}</a>',
    'menu_node_cms' => '페이지 관리',
    'menu_node_cmssettings' => '페이지 설정',
    'menu_group_env' => '페이지 설정',
    'menu_group_item_env_container' => '수집',
    'menu_group_item_env_layouts' => '레이아웃',
    'menu_group_elements' => '컨텐츠 요소',
    'menu_group_item_elements_blocks' => '모듈 관리',
    'menu_group_item_elements_group' => '모듈 그룹',
    'btn_abort' => '중단',
    'btn_refresh' => '새로 만들기',
    'btn_save' => '저장',

// added translations in version 1.0.0-beta3:
    'model_navitemmodule_module_name_label' => '모듈 이름',
    'model_navitem_title_label' => '페이지 제목',
    'model_navitem_alias_label' => 'URL 경로 세그먼트',
    'model_navitempage_layout_label' => '레이아웃',
    'model_navitemredirect_type_label' => '리다이렉션 유형',
    'model_navitemredirect_value_label' => '리다이렉션 대상',

    'view_index_add_title' => '새로운 페이지를 추가하세요',
    'view_index_add_page_from_language' => '언어로부터 페이지를 추가하세요',
    'view_index_add_page_from_language_info' => '이 페이지를 만들 때 다른 언어의 내용을 복사 하시겠습니까?',
    'view_index_add_page_empty' => '새로운 빈 페이지를 추가하세요',
    'view_index_page_loading' => '페이지 로딩중',
    'draft_title' => '템플릿',
    'draft_text' => '기존 템플릿을 편집하십시오. 새 페이지를 만들 때 템플릿을 적용할 수 있습니다.',
    'draft_column_id' => 'ID',
    'draft_column_title' => '제목',
    'draft_column_action' => '실행',
    'draft_edit_button' => '편집',
    'js_added_translation_ok' => '이 페이지의 번역은 성공적으로 실행되었습니다.',
    'js_added_translation_error' => '번역 작업 도중에 오류가 발생했습니다.',
    'js_page_add_exists' => '같은 url을 가진 "%title"페이지는 이미 (id=%id%)그룹에 있습니다.',
    'js_page_property_refresh' => '속성이 업데이트되었습니다.',
    'js_page_confirm_delete' => '이 페이지를 정말로 삭제하기를 원하십니까?',
    'js_page_delete_error_cause_redirects' => '이 페이지는 삭제될 수 없습니다. 이 페이지에서 모든 리다이렉션을 삭제해야만 합니다.',
    'js_state_online' => '%title% 온라인',
    'js_state_offline' => '%title% 오프라인',
    'js_state_hidden' => '숨겨진 %title%',
    'js_state_visible' => '눈에 보이는 %title%',
    'js_state_is_home' => '%title%는 루트 페이지입니다.',
    'js_state_is_not_home' => '%title%는 루트 페이지가 아닙니다',
    'js_page_item_update_ok' => '이 페이지 «%title%» 업데이트 되었습니다.',
    'js_page_block_update_ok' => '그 블록은 «%name%» 업데이트 되었습니다.',
    'js_page_block_remove_ok' => '그 블록은 «%name%» 삭제되었습니다.',
    'js_page_block_visbility_change' => '눈에 보이는 것은 «%name%»성공적으로 바뀌었습니다.',

// added translations in version 1.0.0-beta5:
    'view_update_blockholder_clipboard' => '클립보드',

// added translations in version 1.0.0-beta6:
    'js_page_block_delete_confirm' => '당신은 정말로  «%name%»블록을 삭제하기를 원하십니까?',
    'view_index_page_meta_keywords' => 'SEO분석을 위한 키워드',
    'current_version' => '작업 버전',
    'Initial' => '초기 버전',
    'view_index_page_version_chooser' => '출시된 버전',
    'versions_selector' => '버전',
    'page_has_no_version' => '이 페이지에는 아직 버전이없고 레이아웃이 없습니다. 오른쪽에 추가 아이콘 <i class = "material-icons green-text"> add </i>를 클릭하여 새 버전을 만드십시오.',
    'version_edit_title' => '버전을 수정하십시오',
    'version_input_name' => '이름',
    'version_input_layout' => '레이아웃',
    'version_create_title' => '새로운 버전을 추가하십시오',
    'version_create_info' => '내용이 다른 여러 페이지 버전을 만들 수 있습니다. 웹 사이트에서 볼 수 있도록 버전을 게시하십시오.',
    'version_input_copy_chooser' => '복사 할 버전',
    'version_create_copy' => '기존 버전의 사본을 만드십시오',
    'version_create_new' => '새롭고 빈 공간의 버전을 만드십시오.',
    'js_version_update_success' => '이 버전은 성공적으로 업데이트 되었습니다.',
    'js_version_error_empty_fields' => '하나 이상의 입력란이 비어 있거나 잘못된 값이 있습니다.',
    'js_version_create_success' => '이 새로운 버전은 성공적으로 생성되었습니다.',

// added translations in version 1.0.0-beta7:
    'view_index_create_page_please_choose' => '선택하세요',
    'view_index_sidebar_autopreview' => '자동 미리보기',

// added translations in version 1.0.0-beta8:
    'module_permission_add_new_page' => '새 페이지 만들기',
    'module_permission_update_pages' => '페이지 편집',
    'module_permission_edit_drafts' => '템플릿 편집',
    'module_permission_page_blocks' => '페이지 콘텐츠 블록',
    'js_version_delete_confirm' => ' 페이지 버전«%alias%»을 삭제 하시겠습니까?',
    'js_version_delete_confirm_success' => '페이지 버전 % alias %가 성공적으로 삭제되었습니다.',
    'log_action_insert_cms_nav_item' => '새 언어 <strong>{info}</strong>추가하십시오',
    'log_action_insert_cms_nav' => '새로운 페이지 <strong>{info}</strong>추가하십시오',
    'log_action_insert_cms_nav_item_page_block_item' => '새 블록 <strong> {info} </strong>을 삽입했습니다',
    'log_action_insert_unkown' => '새로운 줄을 삽입하십시오',
    'log_action_update_cms_nav_item' => '<strong> {info} </strong> 페이지의 언어를 업데이트했습니다.',
    'log_action_update_cms_nav' => '<strong> {info} </strong> 페이지의 상태를 업데이트했습니다.',
    'log_action_update_cms_nav_item_page_block_item' => '<strong> {info} </strong> 블록의 콘텐츠 또는 구성을 업데이트했습니다.',
    'log_action_update_unkown' => '기존 행 업데이트 되었습니다.',
    'log_action_delete_cms_nav_item' => '<strong> {info} </strong>의 언어 버전을 삭제했습니다.',
    'log_action_delete_cms_nav' => '삭제 된 페이지 <strong> {info} </strong>',
    'log_action_delete_cms_nav_item_page_block_item' => '삭제 된 블록 <strong> {info} </strong>',
    'log_action_delete_unkown' => '행을 삭제했습니다.',
    'block_group_favorites' => '즐겨 찾기',
    'button_create_version' => '버전 생성하기',
    'button_update_version' => '버전 업데이트 하기',
    'menu_group_item_env_permission' => '페이지 수락',

// added translations in version 1.0.0-rc1:
    'page_update_actions_deepcopy_text' => '모든 내용으로 현재 페이지의 복사본을 만듭니다. 여기에는 모든 언어가 포함되지만 출판 된 버전 만 포함됩니다.',
    'page_update_actions_deepcopy_btn' => '복사본 만들기',

// added translations in version 1.0.0-rc2:
    'model_navitem_title_tag_label' => '제목 태그(SEO)',

// added translations in version 1.0.0-rc3:
    'model_navitempage_empty_draft_id' => '비어있는 템플릿에서는 페이지를 생성할 수 없습니다.',
    'view_update_variation_select' => '표준',
    'menu_group_item_env_config' => '구성',
    'js_config_update_success' => '구성이 성공적으로 업데이트되었습니다.',
    'config_index_httpexceptionnavid' => '리디렉션 될 오류 404 페이지를 지정하십시오.
<br /> <small> 팁 : 오류 메시지가 포함 된 404 페이지를 만들고 숨김으로 표시하십시오.</small>',
    'module_permission_update_config' => 'CMS 구성',
    'module_permission_delete_pages' => '페이지 삭제',
    'page_update_actions_deepcopy_title' => '페이지 복사',
    'page_update_actions_layout_title' => '레이아웃 파일',
    'page_update_actions_layout_text' => '기본 레이아웃 파일 대신 렌더링 할 다른 레이아웃 파일을 지정하십시오 (파일 확장자 ".php"는 생략 가능, 경로 별칭을 사용할 수 있음). 비어 있으면`main.php`가 기본값으로 사용됩니다.',
    'page_update_actions_layout_file_field' => '레이아웃 파일',
    'page_update_actions_modal_title' => '페이지 설정',
    'js_page_update_layout_save_success' => '레이아웃 파일이 업데이트되었습니다.',
    'js_page_create_copy_success' => '페이지 복사본이 생성되었습니다.',
    'view_update_offline_info' => '온라인/오프라인 환경을 바꾸세요. 페이지가 오프라인 인 경우 URL로 액세스 할 수 없습니다.',
    'view_update_hidden_info' => '공개 상태를 변경합니다. 페이지가 숨겨져 있으면 URL로 액세스 할 수 있지만 탐색에서는 숨겨집니다.',
    'view_update_homepage_info' => '이 페이지를 홈페이지로 설정하세요.',
    'view_update_block_tooltip_copy' => '클립보드를 추가하세요',
    'view_update_block_tooltip_visible' => '탐색시 페이지가 보이지 않도록하기',
    'view_update_block_tooltip_invisible' => '탐색시 페이지 표시하기',
    'view_update_block_tooltip_edit' => '편집',
    'view_update_block_tooltip_editcfg' => '구성',
    'view_update_block_tooltip_delete' => '삭제',
    'view_update_block_tooltip_close' => '닫기',

// added translations in version 1.0.0:
    'cmsadmin_dashboard_lastupdate' => '마지막 페이지 업데이트',
    'cmsadmin_settings_homepage_title' => '빈 페이지',
    'cmsadmin_settings_trashpage_title' => '페이지 삭제하기',
    'cmsadmin_settings_modal_title' => '환경',
    'cmsadmin_item_settings_titleslug' => '페이지 정보',
    'cmsadmin_created_at' => '작성 시간',
    'cmsadmin_version_remove' => '버전 삭제',
    'view_index_sidebar_container_no_pages' => '빈 컨테이너',
    'view_update_set_as_homepage_btn' => '홈페이지로 설정하십시오',
    'cmsadmin_settings_time_title' => '스케줄러',
    'cmsadmin_settings_time_title_from' => '부터',
    'cmsadmin_settings_time_title_till' => '까지',
    'view_index_page_meta_timestamp_create' => '페이지 생성 날짜',
    'nav_item_model_error_modulenameexists' => '별칭 "{alias}"는 이미 모듈로 존재합니다. 다른 별칭 이름을 사용하거나 구성에서 이 별칭으로 모듈의 이름을 바꿉니다.',
    'nav_item_model_error_parentnavidcannotnull' =>  '부모 탐색 ID는 null 일 수 없으며 부모 페이지에서 확장하는 동안 문제가 발생했습니다.',
    'nav_item_model_error_urlsegementexistsalready' => '이 별칭은 이미 존재합니다. 다른 이름을 사용하십시오.',
    'menu_group_item_env_redirections' => '리다이렉션',
    'redirect_model_atr_timestamp_create' => '타임 스탬프 생성',
    'redirect_model_atr_catch_path' => '출발 경로',
    'redirect_model_atr_catch_path_hint' => '리디렉션되어야 할 경로입니다.
경로의 모든 하위 경로를 일치 시키려면 / blog 의 모든 하위 경로와 일치하는 /와 같은 * 와일드 카드를 사용할 수 있습니다.',
    'redirect_model_atr_catch_path_error' => '경로는 슬래시로 시작해야합니다.',
    'redirect_model_atr_redirect_path' => '목적지',
    'redirect_model_atr_redirect_path_hint' => 'https : // 또는 http : //로 시작하는 폐지 경로를 사용할 수 있습니다. /로 시작하는 웹 사이트 루트와 관련된 경로 또는 리디렉션되는 경로와 관련된 경로 (예 : 유지 관리에서 / shop / start로 리디렉션) to / shop / maintenance).',
    'redirect_model_atr_redirect_status_code' => 'HTTP 상태 코드',
    'redirect_model_atr_redirect_status_code_hint' => '리디렉션 유형. 301 : Moved Permanently을 사용하는 경우 브라우저는 브라우저의 캐시를 지우지 않고 대상에 대한 변경 사항이 적용되지 않도록 리다이렉션을 캐시합니다.',
    'redirect_model_atr_redirect_status_code_opt_301' => '301 : 영구적 작동',
    'redirect_model_atr_redirect_status_code_opt_302' => '302 : 일시적 작동',

// added translations in version 1.0.1:
    'module_permission_page' => '페이지 내용',

// added translations in version 1.0.6:
    'page_update_actions_deepcopyastemplate_title' => 'Copy as Template',
    'page_update_actions_deepcopyastemplate_text' => 'Create a template of the current page with all its contents. They template will include all languages but only the published version.',
    'page_update_actions_deepcopyastemplate_btn' => 'Create Template',
    'js_page_create_copy_as_template_success' => 'The template has been created.',

// added translations in version 2.0:
    'model_navitem_image_id_label' => 'Image',
    'view_index_page_label_subpage' => 'Select parent page',
    'view_index_page_label_parent_nav_id' => 'Placement in navigation',
    'view_index_page_label_parent_nav_id_root' => 'On the top level',
    'view_index_page_label_parent_nav_id_subpage' => 'As a subpage',
    'cmsadmin_item_settings_titleseo' => 'SEO',
    'cmsadmin_item_settings_titleexpert' => 'Expert',
    'model_navitem_is_url_strict_parsing_disabled_label' => 'Strict URL Parsing',
    'model_navitem_is_url_strict_parsing_disabled_label_enabled' => 'Enabled',
    'model_navitem_is_url_strict_parsing_disabled_label_disabled' => 'Disabled',
    'model_navitem_is_url_strict_parsing_disabled_label_hint' => 'Strict URL parsing should be enabled unless you are using a URL-generating module block in the content of this page.',
    'model_navitem_title_tag_label_hint' => 'The title tag is displayed in many browsers\' title bar and as a page title in the results of search engines.',
    'view_index_page_meta_description_hint' => 'The page description should be a sentence about the purpose of this page. It is often used by search engines as a description for a page in the search results. It is also relevant when sharing a page on social media.',
    'view_index_page_meta_keywords_hint' => 'The keywords are separated by commas (e.g. pizza, burger, pasta). Very few search engines still rely on these keywords. Use only words that are relevant for the page\'s content. The LUYA toolbar of the frontend will help check whether keywords were defined for the current page or not.',
    'model_navitem_image_id_label_hint' => 'The image is important when sharing the page on social media. Usually this image is displayed by social media platforms as a preview image for a page.',
    'view_index_module_controller_name' => 'Controller',
    'view_index_module_action_name' => 'Action',
    'view_index_module_action_params' => 'Action parameters',
    'view_index_module_select_help' => 'Choose the module to display from the list (only frontend modules are listed). Modules must be configured in the modules section of the config.',
    'view_index_module_advanced_settings_button' => 'Advanced settings',

// added translations in version 3.0:
    'menu_group_item_env_themes' => 'Themes',
    'view_index_page_is_cacheable' => 'Caching',
    'view_index_page_is_cacheable_hint' => 'When enabled, the whole page will be cached including all blocks, therefore dynamically generated data in blocks will not be updated.',

// added translations in version 3.3:
    'menu_group_page_display' => 'Page Display',
    'menu_group_configuration' => 'Configuration',
    'menu_group_protocol' => 'Protocol',
    'menu_group_protocol_model_event_logger' => 'Model Event Log',
    'unable_to_find_item_for_language' => 'The requested page has not been translated yet.',

// added translations in version 3.4:
    'js_block_attribute_empty' => '«%label%» cannot be empty.',

// added translations in version 4.0:
    'menu_group_item_env_websites' => 'Websites',
    'model_website_use_default_theme' => 'Use default theme',
    'model_website_all' => 'All',
    'model_website_group_ids_label' => 'Restrict to User Groups',
    'model_website_user_ids_label' => 'Restrict to Users',
    'model_website_access_restrict' => 'Access restriction',

// added translations in version 4.2.0:
    'model_block_translation_name_label' => 'Name',
    'model_block_class_label' => 'Class',
    'model_block_group_id_label' => 'Group',
    'model_block_usage_count_label' => 'Used in content',
    'model_block_file_exists_label' => 'File exists',
    'model_block_is_disable_label' => 'Is disabled',
    'aws_block_pages' => 'Pages',
    'aws_block_pages_title_label' => 'Title',
    'aws_block_pages_language_label' => 'Language',
    'aws_block_pages_version_label' => 'Version',
    'aws_block_pages_block_visible_label' => 'Block visible',
    'aws_block_pages_last_updated_label' => 'Last updated',
    'aws_block_pages_created_label' => 'Created',
    'model_blockgroup_name_label' => 'Name',
    'model_blockgroup_identifier_label' => 'Identifier',
    'model_blockgroup_class_label' => 'Class Name',
    'model_blockgroup_created_timestamp_label' => 'Created at',
    'model_blockgroup_is_deleted_label' => 'Is deleted',
    'model_blockgroup_group_label' => 'Group',
    'model_layout_name_label' => 'Name',
    'model_layout_json_config_label' => 'JSON Config',
    'model_layout_view_file_label' => 'View File',
    'model_log_user_id_label' => 'User',
    'model_log_is_insertion_label' => 'Insert',
    'model_log_is_update_label' => 'Update',
    'model_log_is_deletion_label' => 'Delete',
    'model_log_timestamp_label' => 'Timestamp',
    'model_log_message_label' => 'Message',
    'model_log_data_json_label' => 'Data JSON',
    'model_log_table_name_label' => 'Table Name',
    'model_log_row_id_label' => 'Row ID',
    'model_navcontainer_name_label' => 'Name',
    'model_navcontainer_alias_label' => 'Alias',
    'model_navcontainer_website_label' => 'Website',
    'model_theme_name_label' => 'Name',
    'model_theme_is_default_label' => 'Is default',
    'model_theme_base_path_label' => 'Base Path',
    'model_theme_json_config_label' => 'JSON Config',
    'model_theme_parent_theme_label' => 'Parent Theme',
    'model_theme_author_label' => 'Author',
    'model_website_name_label' => 'Name',
    'model_website_host_label' => 'Host',
    'model_website_aliases_label' => 'Aliases',
    'model_website_is_active_label' => 'Is active',
    'model_website_is_default_label' => 'Is default',
    'model_website_redirect_to_host_label' => 'Redirect to Host',
    'model_website_theme_id_label' => 'Theme',
    'by_label' => 'by',
];
