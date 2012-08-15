	<p class="row1">
		<label><?php echo 'Select type:'; ?></label>
		<em> <?php
			$display_block = '';
			$section_identifier = isset($_POST['section_identifier']) ? $_POST['section_identifier'] : '';
			$content_type = '';
			$section_name = '';
			$post_ids = '';
			$category_id = '';
			$num_posts = '';
			$feed_url = '';
			$post_type = '';
			$is_checked = '';

			$size = '';
			$num_posts_xml_feed = '';

			$sec_obj = new Section;
			$sec_obj->section_identifier = $section_identifier;
			$section_object = $sec_obj->mpo_section_exists();
			if (is_object($section_object)) {
				$content_type = $section_object->section_meta_key;
				$section_name = $section_object->section_name;
				switch ($content_type) {
					case 'specific_content':
						$display_specific_content_block = 1;
						if ( is_serial($section_object->section_meta_value) ) {
							$combined_value = unserialize($section_object->section_meta_value);
							$post_ids = $combined_value['post_ids'];
							$post_type = $combined_value['post_type'];
						} else {
							$post_type = 'post';
							$post_ids = $section_object->section_meta_value;
						}
					break;
					case 'category_radio':
						$display_category_radio_block = 1;
						if ( is_serial($section_object->section_meta_value) ) {
							$combined_value = unserialize($section_object->section_meta_value);
              $is_checked = $combined_value['is_checked'];
              if ($is_checked) {
                $post_ids = $combined_value['post_ids'];
              } else {
                $num_posts = $combined_value['length'];
              }
							$category_id = $combined_value['category_id'];
						} else {//This section of code is for backward compatability
							$category_id = $section_object->section_meta_value;
							$num_posts = $section_object->length;
						}
					break;
					case 'xml_feed':
						$display_xml_feed_block = 1;
						$feed_url = $section_object->section_meta_value;
						$num_posts_xml_feed = $section_object->length;
					break;
					default:
						$display_block = 0;
				}
			}
			global $selection_criteria;
			mpo_display_radio_buttons($selection_criteria, 'content_type', $content_type ); ?>
		</em>
	</p>

	<p class="row1">
		<label><?php echo 'Section Name:';?></label>
		<em><input type="text" size="40" name="section_name" value="<?php echo $section_name; ?>" id="section_name" /></em>
		<input type="hidden" id="post_ids" name="post_ids" value="<?php echo $post_ids; ?>" />
		<input type="hidden" id="category_id" name="category_id" value="<?php echo $category_id; ?>" />
    <input type="hidden" id="is_checked" name="is_checked" value="<?php echo $is_checked; ?>" />
		<input type="hidden" id="num_posts" name="num_posts" value="<?php echo $num_posts; ?>" />
		<input type="hidden" id="post_type" name="post_type" value="<?php echo $post_type; ?>" />
		<input type="hidden" id="feed_url" name="feed_url" value="<?php echo $feed_url; ?>" />
		<input type="hidden" id="num_posts_xml_feed" name="num_posts_xml_feed" value="<?php echo $num_posts_xml_feed; ?>" />
	</p>
	<div id="content_desc" class="row1"> <?php
		if ($post_ids != '' || $category_id != '' || $feed_url != '') { ?>
		  <script type="text/javascript">
			mpo_get_content_type();
		  </script> <?php
		}  ?>
	</div> <?php
	if (empty($section_identifier) ) { ?>
		<p class="row1">
			<label>&nbsp;</label>
			<em>
				<input onclick="validate_form()" type="submit" class="button-primary" value="Submit" />
			</em>
		</p> <?php
	} ?>
	<input type="hidden" name="nonce_field" id="nonce_field" value="<?php echo wp_create_nonce( 'select_criteria' ); ?>"/>
	<input type="hidden" name="selected_entries" id="selected_entries" />