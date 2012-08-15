	<div id="drag_drop_sel">
		<label><?php echo 'Select Posts:';?></label>
		<div id="content1" class="section_content_selection_parent">
			<strong>
				<dfn><?php echo 'Click on Add button to select from latest '. DEFAULT_POSTS_PER_PAGE . ' posts';?></dfn>
				<del>
					<input type="text" name="search_posts_text" value="Search Posts.." id="search_posts_text" size="20" />
					<input type="button" id="search_posts_button" value="search" />
				</del>
			</strong>
			<div id="specific_content_container" class="section_content_child_overflow border" > <?php
				$section_ids_array = array();
				$post_ids = isset($_POST['post_ids']) ? $_POST['post_ids'] : '';
				$post_type = isset($_POST['post_type']) ? $_POST['post_type'] : 'post';
        $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
				$args = array( 'posts_per_page' => DEFAULT_POSTS_PER_PAGE, 'post_status' => 'publish', 'post_type' => $post_type);
				if (isset($category_id) && $category_id != '') {
					$args['cat'] = $category_id;
				}
				if ($post_ids)  {
					$section_ids_array = explode(',', $post_ids);
					$args['post__not_in'] = $section_ids_array;
				}
				require_once ('show_posts.php');//We are using same UI in search ?>
			</div>
			<div class="section_content_response" id="selections" >
				<strong>
					<dfn id="labelSelections"><?php echo 'Drag and Drop Posts to rearrange in any order';?></dfn>
				</strong>
				<div id="labelSelections_" class="tableDemo section_content_child" style="padding:4px;">
					<div class="section_content_child_overflow">
						<table cellspacing="0" cellpadding="2" width="99%" id="table_selections">
							<tbody id="tbody_selections"> <?php
								if ($post_ids)  {
									$section_ids_array = explode(',', $post_ids);
									for($i=0; $i < count($section_ids_array); $i++)  {
										$post_info = get_post($section_ids_array[$i]); ?>
										<tr id="entry_<?php echo $section_ids_array[$i];?>" style="cursor: move;">
											<td style="width:75%"><?php echo $post_info->post_title; ?></td>
											<td><?php echo get_the_time('j M, Y', $post_info->ID); ?></td>
											<td id="action_entry_<?php echo $section_ids_array[$i];?>" style="cursor: auto;">Remove</td>
										</tr> <?php
									}
								} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>