<?php
  $section_identifier = $_POST['section_identifier'];
  if ( !isset($section_identifier) || empty($section_identifier) ){
    echo 'Please select a valid section';
    exit;
  }

  $sec_obj = new Section;
  $sec_obj->section_identifier = $section_identifier;
  $res = $sec_obj->mpo_delete_section();
  if ($res) {
    echo 'Section deleted successfully';
  } else {
    echo 'There was some problem while updation';
  }
  exit;
?>