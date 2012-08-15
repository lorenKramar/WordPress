<?php
 /**
	* Displays radio buttons
  */
	function mpo_display_radio_buttons($selection_array, $radio_button_name, $content_type = '') {
    $count = 0;
    $disabled = '';
    $divisor = '';
		if (!array($selection_array)) {
			return false;
		}
    foreach ($selection_array as $sel_key => $sel_val) {
      $divisor = 3;
      if ($count % $divisor == 0) { ?>
        <kbd> <?php
      }
      if ($content_type == $sel_key) {
        $selected = 'checked="true"';
      } else {
        $selected = '';
      } ?>
      <i>
				<input type="radio" <?php echo $selected; ?>  class="<?php echo $radio_button_name; ?>" name="<?php echo $radio_button_name; ?>"  id="<?php echo $sel_key; ?>" value="<?php echo $sel_key; ?>" >
			</i>
			<small><?php echo $sel_val; ?></small> <?php
      $count++;
      if ($count % $divisor == 0 ) { ?>
        </kbd> <?php
      }
    }
  }

 /**
	* Fetches specific information of a section.
  */
  function mpo_get_section_info($section_identifier, $info = 'theme_file') {
    global $global_section_array;
		if ( !is_array($global_section_array[$section_identifier]) ) {
			return false;
		}
		if (array_key_exists($info, $global_section_array[$section_identifier])) {
			return $global_section_array[$section_identifier][$info];
		} else {
			return false;
		}
	}

 /**
	* This function is used to search Posts
  */
  add_action( 'wp_ajax_search_posts', 'mpo_search_posts' );

  function mpo_search_posts() {
    $_POST      = array_map( 'stripslashes_deep', $_POST );

    $search_str = $_POST['search_str'];
    $args = array('s' => $search_str, 'post_type' => 'any', 'post_status' => 'publish');
    require_once ('show_posts.php');
    exit;
  }


 /**
	* Displays selection box
  */
	function mpo_display_selection_box ($selection_box_name, $max = 5, $selected = 0) { ?>
    <select id="<?php echo $selection_box_name; ?>" name="<?php echo $selection_box_name; ?>"> <?php
      for ($i = 1; $i<= $max; $i++) {
        if ($selected == $i) {
          $sel = 'selected';
        } else {
          $sel = '';
        } ?>
        <option <?php echo $sel; ?> value="<?php echo $i; ?>"> <?php echo $i; ?></option> <?php
      } ?>
    </select> <?php
  }

 /**
	* Displays custom posts order.
  */
  function mpo_specific_content_orderby( $orderby ) {
    global $post_ids;
    $orderby = " FIELD(ID, $post_ids)";
    return $orderby;
  }

 /**
	* Function to check serialized value
  */
  function is_serial($string) {
    return (@unserialize($string) !== false);
	}

/**
	* Displays images' thumbnails
  */
	function mpo_show_thumb ($width, $height, $show_only_path = NULL) {
    global $post;
    if(has_post_thumbnail()) {//This section is for featured image
      if ($show_only_path) {
        $image_arr = array();
        $image_arr = wp_get_attachment_image_src( get_post_thumbnail_id(), array($width,$height), false );
        echo $image_arr[0];
      } else { ?>
        <a href="<?php echo get_permalink($post); ?>"> <?php
          the_post_thumbnail( array($width, $height) ); ?>
        </a> <?php
      }
    } else {
      $images = get_children( array( 'post_parent' => $post->ID, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'orderby' => 'menu_order', 'order' => 'ASC' ) );
			if ( $images ) {
				$image = array_shift( $images );
				$image_img_tag = wp_get_attachment_image( $image->ID, array($width, $height) );
				if ($show_only_path) {
					$image_arr = array();
					$image_arr = wp_get_attachment_image_src( $image->ID, array($width,$height), false );
					echo $image_arr[0];
				} else { ?>
					<a href="<?php the_permalink(); ?>"><?php echo $image_img_tag; ?></a> <?php
				}
			} else {
				return false;
			}
    }
  }

 /**
  * Trims words length
  */
  function mpo_trim_words($string, $word_limit, $tags_to_exclude = NULL) {
    $words = explode(' ', $string, ($word_limit + 1));
    if (count($words) > $word_limit) {
      array_pop($words);
      return implode(' ', $words) . '...';
    } else {
      return $string;
    }
  }

 /**
  * Removes sticky posts
  */
  add_filter('pre_get_posts', 'mpo_custom_pre_get_posts');
  function mpo_custom_pre_get_posts($query)  {
     global $wp_query, $post_ids;
    $section_name = isset($query->query_vars['section_name']) ? $query->query_vars['section_name'] : '';
    if ($section_name != '') {
      if ( !isset($query->query_vars['ignore_sticky_posts']) ) {
        $query->query_vars['ignore_sticky_posts'] = 1;
      }
    }
    return $query;
  }


 /**
  * Resets variables on each request
  */
  add_filter('posts_request', 'mpo_reset_query_var');

  function mpo_reset_query_var($request) {
    global $wp_query, $post_ids;
    $wp_query->query_vars['section_name'] = NULL;
    $post_ids = '';

    return $request;
  }

 /**
  * Changes posts' order as per section name.
  */
  add_filter('posts_orderby', 'mpo_change_orderby', 100 );

  function mpo_change_orderby( $orderby ) {
    global $wp_query, $post_ids;
    $section_name = isset($wp_query->query_vars['section_name']) ? $wp_query->query_vars['section_name'] : '';
    if ($section_name != '' && $post_ids != '') {
      $orderby = " FIELD(ID, $post_ids)";
    }
    return $orderby;
  }


 /**
  * Changes posts' limit as per section name.
  */
  add_filter('post_limits', 'mpo_change_limit' );
  function mpo_change_limit( $limit ) {
    global $paged, $myOffset, $wp_query;
    $section_name = isset($wp_query->query_vars['section_name']) ? $wp_query->query_vars['section_name'] : '';
    if ($section_name != '' ) {
      if (empty($paged)) {
        $paged = 1;
      }
      $postperpage = intval(get_option('posts_per_page'));
      $pgstrt = ((intval($paged) -1) * $postperpage) + $myOffset . ', ';
      $limit = 'LIMIT ' . $pgstrt . $postperpage;
      //$limit = ' LIMIT 0, 100';
    }
    return $limit;
  }

 /**
  * Changes posts' condition as per section name.
  */
  add_filter('posts_where', 'mpo_change_condition' );

  function mpo_change_condition( $where ) {
    global $wp_query, $post_ids;

    $section_name = isset($wp_query->query_vars['section_name']) ? $wp_query->query_vars['section_name'] : '';
    if ($section_name != '' ) {
      $sec_obj = new Section;
      $sec_obj->section_name = $section_name;
      $sec_obj->status = 1;
      $section_data = $sec_obj->mpo_get_section_data_by_name();


      $menu_options = isset($section_data->section_meta_key) ?  $section_data->section_meta_key : '';
      switch($menu_options) {
        case 'specific_content' :
          if ( is_serial($section_data->section_meta_value) ) {
            $combined_value = unserialize($section_data->section_meta_value);
            $post_ids = $combined_value['post_ids'];
            $post_type = $combined_value['post_type'];
            if ($post_type != 'post') {
              $where = str_replace("_posts.post_type = 'post'", "_posts.post_type = '$post_type'", $where);
            }
          } else {
              $post_type = 'post';
              $post_ids = $section_data->section_meta_value;
          }

        break;
        case 'category_radio' :
          if ( is_serial($section_data->section_meta_value) ) {
              $combined_value = unserialize($section_data->section_meta_value);
            $is_checked = $combined_value['is_checked'];
            if ($is_checked) {
              $post_ids = $combined_value['post_ids'];
            } else {
              $length = $combined_value['length'];
            }
              $category_id = $combined_value['category_id'];
          } else {//This section of code is for backward compatability
              $category_id = $section_data->section_meta_value;
              $length = $section_data->length;
          }
        break;
        case 'xml_feed' :
          return $where;
        break;
        default:
          return $where;
      }
      $where .= 'AND wp_posts.ID IN (' . $post_ids . ')';
    }
    return $where;
  }
