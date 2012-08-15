	<div class="selection_criteria" >
		<div class="popupmain" style="float:left;">
			<p class="req_head"><?php echo 'Choose your criteria';?></p>
			<div class="formfield">
				<p class="row1">
					<label><?php echo 'Select option:'; ?></label>
					<em> <?php
						$main_criteria = array('add_section' => 'Add new section', 'edit_section' => 'Edit / Delete section');
						mpo_display_radio_buttons($main_criteria, 'main_criteria'); ?>
					</em>
				</p>
				<div id="add_edit_section_row"></div>
			</div>
		</div>
	</div>


	<div style="float:left;margin-top:100px;width:98%">
		<p class="reqd_head"><b>Previously created sections :</b></p> <?php
		$sec_obj = new Section;
		$all_sections = $sec_obj->mpo_get_all_sections();
		global $selection_criteria;
		if (count($all_sections) > 0) { ?>
			<table width="100%" class="widefat">
				<thead>
					<tr>
						<th>S.no</th>
						<th>Section name</th>
						<th>Content type</th>
						<th>Action</th>
					</tr>
				</thead> <?php
				$counter = 1;
				foreach ($all_sections as $section) { ?>
					<tr>
						<td valign="top"><?php echo $counter; ?></td>
						<td valign="top"><?php echo $section->section_name; ?></td>
						<td valign="top"><?php echo isset($selection_criteria[$section->section_meta_key]) ? $selection_criteria[$section->section_meta_key] : ''; ?></td>
						<td valign="top">
							<a onclick="edit_section('<?php echo $section->section_identifier; ?>');return false;" href="#;">Edit</a> /
							<a onclick="section_delete('<?php echo $section->section_identifier; ?>');return false;" href="#;">Delete</a>
						</td>
					</tr> <?php
					$counter++;
				} ?>
			</table> <?php
		} else {
			echo 'No section found';
		} ?>
	</div>