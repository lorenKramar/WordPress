<?php
/*
Plugin Name: My Posts Order
Description: A plugin which allows you to sort posts, pages, custom post type in ANY order.
Author: Kapil Chugh
Author URI: http://kapilchugh.wordpress.com/
Version: 1.2.1
*/

 	include 'includes/db-schema.php';//Custom table is added
	require_once 'includes/defines.php';
	require_once 'includes/functions.php';
	require_once 'includes/widget.php';
	require_once 'classes/Section/Section.php';


	register_activation_hook(__FILE__, 'install_sections_table');

 /**
	* Includes CSS and Javascript
  */
	//add_action( 'wp_print_scripts', 'mpo_custom_theme_scripts', 100);
  function mpo_custom_theme_scripts() {
		wp_enqueue_script( 'jquery');
    wp_enqueue_script( 'my_posts_order', MPO_JS_PATH . 'my_posts_order.js', 'jquery', '1.0', true );
    wp_enqueue_script( 'tablednd', MPO_JS_PATH . 'jquery.tablednd.js' ); ?>
    <script type="text/javascript">var MPO_IMAGES_PATH = '<?php echo MPO_IMAGES_PATH; ?>';</script>
    <link rel="stylesheet" href="<?php echo MPO_CSS_PATH; ?>theme-editor.css" type="text/css" media="screen" /> <?php
  }




 /**
	* Includes menu option
  */
  add_action('admin_menu', 'mpo_add_custom_admin_page');
	function mpo_add_custom_admin_page() {
		$mpo_mypage = add_menu_page('My Posts Order Options', 'My Posts Order Options', 'edit_published_posts', 'my-posts-order', 'mpo_custom_optons_posts_order');
		//loads JS and CSS only on this page not on all Admin pages.
    add_action( "admin_print_scripts-$mpo_mypage", 'mpo_custom_theme_scripts');
	}

 /**
	* First function that will be called
  */
	function mpo_custom_optons_posts_order () {
		require_once('includes/select_criteria.php');
	}

 /**
	* Saves data in custom table
  */
	add_action( 'wp_ajax_save_section_data', 'mpo_save_section_data' );
  function mpo_save_section_data() {
    require_once ('includes/save_section_data.php');
    exit;
  }

 /**
	* Adds and Edits
  */
  add_action( 'wp_ajax_add_edit_section', 'mpo_add_edit_section' );

  function mpo_add_edit_section() {
    require_once ('includes/add_edit_criteria.php');
    exit;
  }

 /**
	* Edits section
  */
  add_action( 'wp_ajax_edit_section', 'mpo_edit_section' );
  function mpo_edit_section() {
    require_once ('includes/edit_section.php');
    exit;
  }

 /**
	* Deletes section
  */
  add_action( 'wp_ajax_delete_section_data', 'mpo_delete_section_data' );

  function mpo_delete_section_data() {
    require_once ('includes/delete_section_data.php');
    exit;
  }

 /**
	* Generates 'Settings' link on plugin page
  */
  add_filter( 'plugin_action_links', 'mpo_plugin_action_links', 10, 2 );

  function mpo_plugin_action_links( $links, $file ) {
    if ( $file == plugin_basename( dirname(__FILE__) . '/my-posts-order.php' ) ) {
      $links[] = '<a href="admin.php?page=my-posts-order">'.__('Settings').'</a>';
    }

    return $links;
  }

   /**
  * Gives Drag and Drop options
  */
  add_action( 'wp_ajax_get_content_type', 'mpo_get_content_type' );
  function mpo_get_content_type() {
    require_once ('includes/get_content_type.php');
    exit;
  }

   /**
  * Gives Drag and Drop criteria
  */
  add_action( 'wp_ajax_drag_drop_criteria', 'mpo_drag_drop_criteria' );
  function mpo_drag_drop_criteria  () {
    require_once ('includes/drag_drop_criteria.php');
    exit;
  }
?>