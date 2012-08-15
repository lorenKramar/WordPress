<?php
  $path = WP_PLUGIN_URL . '/' . 'my-posts-order';
  define('PLUGIN_PATH', $path);
  define('STATIC_PATH', PLUGIN_PATH . '/static/');
  define('MPO_JS_PATH', STATIC_PATH . 'js/');
  define('MPO_CSS_PATH', STATIC_PATH . 'css/');
  define('MPO_IMAGES_PATH', STATIC_PATH . 'images/');
  define('AJAX_SEPARATOR', '~#$');
  define('DEFAULT_POSTS_PER_PAGE', 300);
  define('DEFAULT_NUM_POSTS', 20);


  define('MPO_CUSTOM_TEMPLATE', plugin_dir_path(__FILE__) . 'custom-templates/');

  global $selection_criteria, $global_section_array;
  $selection_criteria = array('specific_content' => 'Select posts individually', 'category_radio' => 'Select a category', 'xml_feed' => 'Show XML/RSS Feed');

?>