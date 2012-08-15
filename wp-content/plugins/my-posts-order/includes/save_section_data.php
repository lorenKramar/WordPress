<?php
  check_ajax_referer( "select_criteria" );
  global $selection_criteria;

  $_POST      = array_map( 'stripslashes_deep', $_POST );
  $_GET       = array_map( 'stripslashes_deep', $_GET );
  $_COOKIE    = array_map( 'stripslashes_deep', $_COOKIE );
  $_REQUEST   = array_map( 'stripslashes_deep', $_REQUEST );

    // Escape with wpdb.

  $content_type = isset($_POST['content_type']) ? $_POST['content_type'] : '';
  $section_identifier = isset($_POST['section_identifier']) ? $_POST['section_identifier'] : uniqid();
  $post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : '';

  $is_checked = (isset($_POST['is_checked']) && $_POST['is_checked'] == 1) ? 1 : 0;
  $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
  $feed_url = isset($_POST['feed_url']) ? $_POST['feed_url'] : '';
  $length = isset($_POST['length']) ? $_POST['length'] : '';
  $section_name = isset($_POST['section_name']) ? trim($_POST['section_name']) : '';
  $post_type = isset($_POST['post_type']) ? trim($_POST['post_type']) : '';

  if (isset($section_identifier) && !empty($section_identifier)) {
    global $selection_criteria;
    $error_mess = array();
    $ERROR = false;
    if (!array_key_exists($content_type, $selection_criteria)) {
      $error_mess[] = 'This selection criteria doest not exist';
      $ERROR = true;
    }
    $sec_obj = new Section;
    $sec_obj->section_identifier = $section_identifier;
    $sec_obj->section_meta_key = $content_type;
    $sec_obj->section_name = $section_name;

    $edit_mode = 0;
    if (isset($_POST['section_identifier'])) {
      $edit_mode = 1;
    }
    //if (! isset($_POST['section_identifier']) ) {//We are inserting a new record
      $check_duplicate_vals = $sec_obj->mpo_duplicate_records($edit_mode);
      if ( count($check_duplicate_vals) >0) {
        echo 'Section name exists already' . AJAX_SEPARATOR . '0';
        exit;
      }
    //}
    switch($content_type) {
      case 'specific_content':
        if (empty($post_ids)) {
          $error_mess[] = 'Please select some post';
          $ERROR = true;
        }
        $combined_value = array('post_type' => $post_type, 'post_ids' => $post_ids);
        $sec_obj->section_meta_value = serialize($combined_value);
      break;
      case 'category_radio':
        if (empty($category_id)) {
          $error_mess[] = 'Please choose a category';
          $ERROR = true;
        }
        if ($is_checked) {
          $combined_value = array('category_id' => $category_id, 'is_checked' => 1, 'post_ids' => $post_ids);
        } else {
          $combined_value = array('category_id' => $category_id, 'is_checked' => 0, 'length' => $length);
          $sec_obj->length = $length;
        }
        $sec_obj->section_meta_value = serialize($combined_value);
      break;
      case 'xml_feed':
        if (empty($feed_url)) {
          $error_mess[] = 'Please enter a valid url';
          $ERROR = true;
        }
        $sec_obj->section_meta_value = $feed_url;
        $sec_obj->length = $length;
      break;
    }
    $sec_obj->status = 1;
		if ($ERROR === true) {
      echo $error_mess[0] . AJAX_SEPARATOR . '0';
    } else {
       //$filename = get_section_info($section_identifier, 'theme_file');TODO later on will provide page wise section settings also
      $sec_obj->theme_file = '';
      $result = $sec_obj->mpo_save_section_data();
      if ($result) {
				if ($edit_mode) {
					echo 'Section updated successfully' . AJAX_SEPARATOR . '1';
				} else {
					echo 'Section created successfully' . AJAX_SEPARATOR . '1';
				}
      } else {
        echo 'Sorry could not update data in the database' . AJAX_SEPARATOR . '0';
      }
    }
  }
?>