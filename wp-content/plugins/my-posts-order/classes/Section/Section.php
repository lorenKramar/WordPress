<?php
  class Section {
    public $section_identifier;

    public $content_type;

    public $post_ids;

    public $status = 0;

    public $length = 0;

    public $section_name = '';

	 /**
	  * Saves section data
    */
    function mpo_save_section_data () {
      global $wpdb, $user_ID;
      if ($this->section_identifier) {
         if ($wpdb->get_var( $wpdb->prepare("SELECT section_id FROM {$wpdb->prefix}sections WHERE section_identifier = %s ", $this->section_identifier ) )) {
           $wpdb->query($wpdb->prepare("DELETE FROM {$wpdb->prefix}sections WHERE section_identifier = %s ", $this->section_identifier));
         }
        if (false === $wpdb->insert( "{$wpdb->prefix}sections", array( 'section_identifier' => $this->section_identifier, 'section_name' => $this->section_name,
            'section_meta_key' => $this->section_meta_key, 'section_meta_value' => $this->section_meta_value, 'length' => $this->length,
          'created_by' => $user_ID, 'created_on' => time(), 'theme_file' => $this->theme_file, 'status' => $this->status),
            array( '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%d', '%d', '%s', '%d' ) ) ) {
          return false;
        } else {
          return true;
        }
      }
    }

   /**
	  * Fetches section data
    */
    function mpo_get_section_data () {
      global $wpdb;
      if ($this->section_identifier) {
        $res = $wpdb->get_row( $wpdb->prepare("SELECT section_name, section_meta_key, section_meta_value, length FROM {$wpdb->prefix}sections WHERE section_identifier = %s AND status = %s ORDER BY created_on DESC LIMIT 1", $this->section_identifier, $this->status) );
        return $res;
      }
    }

		/**
	   * Verifies if a section exists
     */
    function mpo_section_exists () {
      global $wpdb;
      if ($this->section_identifier) {
         $res = $wpdb->get_row( $wpdb->prepare("SELECT section_name, section_meta_key, section_meta_value, length  FROM {$wpdb->prefix}sections WHERE section_identifier = %s", $this->section_identifier) );
         return $res;
      }
    }

   /**
	  * Gets value of a section
    */
    function mpo_get_section_var ($var) {
      global $wpdb;
      if ($this->section_identifier && !empty($var)) {
         $res = $wpdb->get_var( $wpdb->prepare("SELECT $var FROM {$wpdb->prefix}sections WHERE section_identifier = %s", $this->section_identifier) );
         return $res;
      }
    }

   /**
	  * Checks duplicate records
    */
    function mpo_duplicate_records ($edit_mode) {
       global $wpdb;
       if ($this->section_identifier) {
         if ($edit_mode) {
          $res = $wpdb->get_row( $wpdb->prepare("SELECT section_name FROM {$wpdb->prefix}sections WHERE section_name = %s AND section_identifier != %s", $this->section_name, $this->section_identifier), ARRAY_A );
         } else {
           $res = $wpdb->get_row( $wpdb->prepare("SELECT section_name FROM {$wpdb->prefix}sections WHERE section_name = %s", $this->section_name), ARRAY_A );
         }
         return $res;
       }
    }

    /**
	  * Updates section data
    */
   function mpo_update_section_status () {
      global $wpdb;
      $wpdb->update( "{$wpdb->prefix}sections", array( 'status' => 1 ), array( 'status' => 0 ), array( '%d'), array( '%d' ) );
    }

   /**
	  * Saves section data
    */
    function mpo_get_all_sections () {
      global $wpdb;
        $res = $wpdb->get_results( $wpdb->prepare("SELECT section_identifier, section_name, section_meta_key, section_meta_value, length FROM {$wpdb->prefix}sections "), OBJECT_K );
        return $res;
    }

   /**
	  * Deletes section data
    */
    function mpo_delete_section () {
      global $wpdb;
      if ($this->section_identifier) {
        $res = $wpdb->query( $wpdb->prepare("DELETE FROM {$wpdb->prefix}sections WHERE section_identifier = %s ", $this->section_identifier));
        return $res;
      } else {
        return false;
      }
    }

   /**
    * Fetches section data by section name
    */
    public function mpo_get_section_data_by_name () {
      global $wpdb;
      if ($this->section_name) {
        $res = $wpdb->get_row( $wpdb->prepare("SELECT section_name, section_meta_key, section_meta_value, length FROM {$wpdb->prefix}sections WHERE section_name = %s AND status = %s ORDER BY created_on DESC LIMIT 1", $this->section_name, $this->status) );
        return $res;
      }
    }

  }//End of Class
?>