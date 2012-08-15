	<p class="row1">
		<label><?php echo 'Select section:'; ?></label>
		<em> <?php
			$sec_obj = new Section;
			$all_sections = $sec_obj->mpo_get_all_sections();
			$section_identifier = isset ($_POST['section_identifier']) ? $_POST['section_identifier'] : '';
			if (count($all_sections) > 0 ) { ?>
				<select onchange="edit_posts_section(this.value)" id="all_sections" name="all_sections">
					<option value="">Select Section Name</option><?php
					foreach ($all_sections as $_all_section) {
						if ($section_identifier == $_all_section->section_identifier) {
							$selected = 'selected="true"';
						} else {
							$selected = '';
						} ?>
						<option <?php echo $selected; ?> value="<?php echo $_all_section->section_identifier; ?>"><?php echo $_all_section->section_name; ?></option> <?php
					} ?>
				</select> <?php
			} else {
				echo 'No Section Found';
			} ?>
		</em>
	</p>
	<div id="section_ajax_container"></div>
	<p class="row1">
		<label>&nbsp;</label>
		<em>
			<input onclick="validate_form()" type="submit" class="button-primary" value="Update" />
      <input onclick="confirm_delete()" type="submit" class="button-primary" value="Delete" />
		</em>
	</p>