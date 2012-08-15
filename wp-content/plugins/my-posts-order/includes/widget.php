<?php
 /**
	* Registers 'My Post Order Widget
  */
	add_action( 'widgets_init', 'my_posts_order_widget' );

  function my_posts_order_widget() {
    register_widget( 'My_Posts_Order' );
  }

  class My_Posts_Order extends WP_Widget {

    function My_Posts_Order() {
      $widget_ops = array('classname' => 'my_posts_order', 'description' => "A widget which allows you to sort posts, pages, custom post type in ANY order." );

      $this->WP_Widget('my_posts_order', 'My Posts Order', $widget_ops);
      $this->alt_option_name = 'my_posts_order';
    }

    function widget($args, $instance) {
      extract($args);
      $title = $instance['title'];
      $section_identifier = $instance['section_name'];
      if (isset($section_identifier) ) {
				$sec_obj = new Section;
				$sec_obj->section_identifier = $section_identifier;
				$sec_obj->status = 1;
				$section_data = $sec_obj->mpo_get_section_data();

				$menu_options = isset($section_data->section_meta_key) ?  $section_data->section_meta_key : '';
				global $post_ids;
				switch($menu_options) {
					case 'specific_content' :
						if ( is_serial($section_data->section_meta_value) ) {
							$combined_value = unserialize($section_data->section_meta_value);
							$post_ids = $combined_value['post_ids'];
							$post_type = $combined_value['post_type'];
						} else {
							$post_type = 'post';
							$post_ids = $section_data->section_meta_value;
						}
						$post_ids_array = explode(',', $post_ids);
						$length = count($post_ids_array);
						if (is_array($post_ids_array)) {
							add_filter('posts_orderby', 'mpo_specific_content_orderby' );
							$args = array(
												'orderby' => 'none',
												'post__in'  => $post_ids_array,
												'posts_per_page' => $length,
												'ignore_sticky_posts' => 1,
												'post_type' => $post_type
											);
							include('display_type.php'); //We are not including it require_once because we need different output each time
							remove_filter('posts_orderby', 'mpo_specific_content_orderby' );
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

						if ($is_checked) {
							$post_ids_array = explode(',', $post_ids);
							$length = count($post_ids_array);
							if (is_array($post_ids_array)) {
								add_filter('posts_orderby', 'mpo_specific_content_orderby' );
								$args = array(
													'orderby' => 'none',
													'post__in'  => $post_ids_array,
													'posts_per_page' => $length,
													'ignore_sticky_posts' => 1,
												);
								include('display_type.php'); //We are not including it require_once because we need different output each time
								remove_filter('posts_orderby', 'mpo_specific_content_orderby' );
							}
						} else {
							$args = array(
									'cat' => $category_id,
									'orderby'=>'ID',
									'order' => 'DESC',
									'posts_per_page' => $length,
									'ignore_sticky_posts' => 1
							);
							include ('display_type.php');
						}

					break;
					case 'xml_feed' :
						$length = $section_data->length;
						include_once(ABSPATH . WPINC . '/feed.php');
						$res_feed_url = $section_data->section_meta_value;
						$rss = fetch_feed($res_feed_url);// Get a SimplePie feed object from the specified feed source.
						if (!is_wp_error( $rss ) ) { // Checks that the object is created correctly
							$maxitems = $rss->get_item_quantity($length);// Figure out how many total items there are, but limit it to 5.
							$rss_items = $rss->get_items(0, $maxitems);// Build an array of all the items, starting with element 0 (first element).
							include ('display_type.php');
						} else {
							echo 'Could not get any feed for this url';
							return;
						}
					break;
					default:
						return '';
				}
      }
    }

    function update( $new_instance, $old_instance ) {
      $instance = $old_instance;
      $instance['title'] = strip_tags($new_instance['title']);
      $instance['section_name'] = $new_instance['section_name'];
			$instance['display_type'] = (int) $new_instance['display_type'];
      return $instance;
    }

    function form( $instance ) {
      $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
      $section_name = isset($instance['section_name']) ? $instance['section_name'] : '';
			$sec_obj = new Section;
			$all_sections = $sec_obj->mpo_get_all_sections();
			if (count($all_sections) > 0 ) { ?>
				<p>
					<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
				</p>
				<p>
					<label for="<?php echo $this->get_field_id('section_name'); ?>"><?php echo 'Select section name'; ?></label>
					<select class="widefat" id="<?php echo $this->get_field_id('section_name'); ?>" name="<?php echo $this->get_field_name('section_name'); ?>">
						<option value="">Choose your option</option> <?php
						foreach ($all_sections as $_all_section) { ?>
							<option value="<?php echo $_all_section->section_identifier; ?>" <?php if($_all_section->section_identifier == $section_name) { echo ' selected = "selected" ';} ?>><?php echo $_all_section->section_name; ?></option> <?php
						} ?>
					</select>
				</p>
				<p>
          <label for="<?php echo $this->get_field_id("display_type"); ?>">
          <?php _e('Select display type'); ?>:
            <select name="<?php echo $this->get_field_name("display_type"); ?>" id="<?php echo
              $this->get_field_id("display_type"); ?>" >
              <option value="1" <?php if ($instance['display_type'] == '1') { echo "selected='selected'";}?> > Display as a list</option>
              <option value="2" <?php if ($instance['display_type'] == '2') { echo "selected='selected'";}?>> Display as a drop down</option>
              <option value="3" <?php if ($instance['display_type'] == '3') { echo "selected='selected'";}?>> Custom Template</option>
            </select>
          </label>
        </p><?php
			} else { ?>
				<div class="widget-control-actions">
					<a href="admin.php?page=my-posts-order">Please create a section</a>
				</div> <?php
			}
    }

  }//end of class