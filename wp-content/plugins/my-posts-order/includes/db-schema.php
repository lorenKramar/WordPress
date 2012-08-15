<?php
  global $wpdb;
  $wpdb->sections = $wpdb->prefix . 'sections';
  
  function install_sections_table () {
    global $wpdb;
    $charset_collate = '';

    if ( ! empty($wpdb->charset) ) {
      $charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
    }
    if ( ! empty($wpdb->collate) ) {
      $charset_collate .= " COLLATE $wpdb->collate";
    }
    if ( is_admin() ) {
      $wpdb->query( "CREATE TABLE IF NOT EXISTS $wpdb->sections (
				`section_id` int(11) NOT NULL AUTO_INCREMENT,
				`section_identifier` varchar(256) ,
				`section_name` varchar(256) DEFAULT NULL,
				`section_meta_key` varchar(256) NOT NULL,
				`section_meta_value` longtext NOT NULL,
				`length` tinyint(4) NOT NULL,
				`created_by` int(11) NOT NULL,
				`created_on` int(11) NOT NULL,
				`theme_file` varchar(128) NOT NULL,
				`status` tinyint(4) NOT NULL,
				PRIMARY KEY (`section_id`)
				) $charset_collate;"
			);
    }
  }
  ?>