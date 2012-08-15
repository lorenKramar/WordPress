<?php
	if ($_POST['content_type'] == 'specific_content') { ?>
		<p class="row1">
      <label><?php echo 'Select post type:';?></label>
      <em> <?php
				$args=array(
					'public'   => true
				);
				$output = 'objects'; // or objects
				$post_types = get_post_types($args, $output);
				$count = 0;
				$get_post_type = $_POST['post_type'] != '' ? $_POST['post_type'] : 'post';
				foreach ($post_types  as $post_type ) {
					$divisor = 3;
					if ($count % $divisor == 0) { ?>
						<kbd> <?php
					}
					if ( $post_type->name != 'attachment' ) { ?>
						<i>
							<input type="radio" class="post_type" name="post_type" value="<?php echo $post_type->name; ?>" <?php If ( ($post_type->name == $get_post_type) ) { echo 'checked="checked"'; } ?> />
						</i>
						<small>
							<?php echo $post_type->label; ?>
						</small> <?php
						$count++;
						if ($count % $divisor == 0 ) { ?>
							</kbd> <?php
						}
					}
				} ?>
      </em>
    </p> <?php
    require_once 'drag_drop_criteria.php';
  } elseif ($_POST['content_type'] == 'category_radio') {
		$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
		$num_posts = isset($_POST['num_posts']) ? $_POST['num_posts'] : '';
    $is_checked = isset($_POST['is_checked']) ? $_POST['is_checked'] : '';
    $post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : '';?>
		<p id="category_desc" class="row1">
			<label>Choose Category:</label>
			<em> <?php
				wp_dropdown_categories( array( 'name' => 'categories_list', 'selected' => $category_id, 'orderby' => 'name', 'hierarchical' => 1 ) ); ?>
			</em>
			<span <?php if ($is_checked == 1) { ?> style="visibility:hidden" <?php } ?> id="num_posts_row">
				<label>Number of Posts:</label>
				<em><?php mpo_display_selection_box('no_posts_category', DEFAULT_NUM_POSTS, $num_posts); ?></em>
			</span>
			<span>
				<label>Want to reorder?:</label>
				<em><input type="checkbox" <?php if ($is_checked) { echo 'checked="true"'; } ?> name="cat_reorder" id="cat_reorder" value="cat_reorder" /></em>
			</span>
		</p>
    <div id="show_hide_drag_drop"> <?php
      if ($is_checked) { ?>
        <script type="text/javascript">
          mpo_fetch_drag_drop_options();
        </script> <?php
      } ?>
    </div> <?php
	} elseif ($_POST['content_type'] == 'xml_feed') {
		$feed_url = isset($_POST['feed_url']) ? $_POST['feed_url'] : '';
		$num_posts_xml_feed = isset($_POST['num_posts_xml_feed']) ? $_POST['num_posts_xml_feed'] : ''; ?>
		<p id="xml_feed_desc" class="row1">
			<label>Enter Xml Feed Url:</label>
			<em><input type="text" size="50" name="xml_feed_url" value="<?php echo $feed_url; ?>" id="xml_feed_url" /> </em>
			<span>
				<label>Number Of Posts:</label>
				<em><?php mpo_display_selection_box('no_posts_xml_feed', 20, $num_posts_xml_feed); ?></em>
			</span>
		</p> <?php
	} ?>