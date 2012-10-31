<?php

/*

Plugin Name: Associated Posts Pro
Plugin URI: http://dennishoppe.de/wordpress-plugins/associated-posts-pro
Description: As the name suggests the "Associated Posts" Plugin enables you to associate posts, pages and custom post types with each other. You can easily select a set of posts (in this case: all types of posts such as posts, pages and custom post types) by their taxonomies, by their authors and of course explicitly.
Version: 1.3.20
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de

*/


// Plugin Class
If (!Class_Exists('wp_plugin_associated_posts')){
class wp_plugin_associated_posts {
  var $base_url;
  var $template_dir;
  var $arr_option_box;
  var $post_object_cache;
  var $saved_options = False;

  function __construct(){
    // Read base
    $this->base_url = get_bloginfo('wpurl').'/'.Str_Replace("\\", '/', SubStr(RealPath(DirName(__FILE__)), Strlen(ABSPATH)));

    // Template directory
    $this->template_dir = WP_CONTENT_DIR . '/associated-posts';

    // Option boxes
    $this->arr_option_box = Array( 'main' => Array(), 'side' => Array() );

    // Post object cache
    $this->post_object_cache = Array();

    // This Plugin supports post thumbnails
    If (function_Exists('Add_Theme_Support')) Add_Theme_Support('post-thumbnails');

    // Hooks & Styles
    Add_Action ('widgets_init', Array($this, 'Load_TextDomain'));
    If (Is_Admin()){
      Add_Action ('admin_menu',      Array($this, 'Add_Options_Page')     );
      Add_Action ('admin_menu',      Array($this, 'Add_Post_Meta_Boxes')  );
      Add_Action ('save_post',       Array($this, 'Save_Meta_Box')        );
      Add_Filter ('pre_set_site_transient_update_plugins', Array($this, 'Check_For_Update'));
      Add_Filter ('plugins_api', Array($this, 'plugins_api'), 10, 3);
    }
    Else {
      Add_Action ('the_post',        Array($this, 'The_Post')             );
      Add_Filter ('the_content',     Array($this, 'Set_Current_Page'), -99);
      Add_Filter ('the_content',     Array($this, 'Filter_Content'), $this->get_option('content_filter_priority') );
      Remove_Filter('the_content',   'do_shortcode', 11 );
      Add_Filter('the_content',      'do_shortcode', 20 );
      Add_Action ('wp_print_styles', Array($this, 'Add_Templates_Styles')   );

      // Shortcodes
      Add_Shortcode ( 'associated-posts', Array($this, 'ShortCode')      ); // Deprecated, will be removed in 1.4
      Add_Shortcode ( 'associated_posts', Array($this, 'ShortCode')      );

      If ($this->get_option('show_page_navigation')) Add_Filter('the_content', Array($this, 'Add_Page_Navigation'), 15 );
    }

    // Set Globals link
    $GLOBALS[__CLASS__] = $this;
  }

  function Load_TextDomain(){
    $locale = Apply_Filters( 'plugin_locale', get_locale(), __CLASS__ );
    Load_TextDomain (__CLASS__, DirName(__FILE__).'/language/' . $locale . '.mo');
  }

  function t ($text, $context = ''){
    // Translates the string $text with context $context
    If ($context == '')
      return Translate ($text, __CLASS__);
    Else
      return Translate_With_GetText_Context ($text, $context, __CLASS__);
  }

  function Default_Options(){
    return Array(
      'posts_position' => 'bottom',
      'content_filter_priority' => 9
    );
  }

  function Save_Options(){
    // Check if this is a post request
    If (Empty($_POST)) return False;

    // Clean the Post array
    $_POST = StripSlashes_Deep($_POST);
    ForEach ($_POST AS $option => $value)
      If (!$value) Unset ($_POST[$option]);

    // Save Options
    Update_Option (__CLASS__, $_POST);

    // We delete the update cache
    $this->Clear_Plugin_Update_Cache();

    return True;
  }

  function Get_Option($key = Null, $default = False){
    // Read Options
    $options = get_option(__CLASS__);
    If (!$options) $options = get_option('wp_plugin_associated_posts_pro'); // backward compatibility

    // Load Default Options
    If (Empty($options)) $arr_option = $this->Default_Options();
    Else $arr_option = Array_Merge ( $this->Default_Options(), $options );

    // Locate the option
    If ($key == Null)
      return $arr_option;
    ElseIf (IsSet ($arr_option[$key]))
      return $arr_option[$key];
    Else
      return $default;
  }

  function Add_Options_Page (){
    $handle = Add_Options_Page(
      $this->t('Associated Posts Options'),
      $this->t('Associated Posts'),
      'manage_options',
      __CLASS__,
      Array($this, 'Print_Options_Page')
    );

    // Add option boxes
    $this->Add_Option_Box ( $this->t('Position'), DirName(__FILE__).'/option-box-position.php' );
    $this->Add_Option_Box ( $this->t('Templates'), DirName(__FILE__).'/option-box-templates.php' );
    $this->Add_Option_Box ( $this->t('Add a Template'), DirName(__FILE__).'/option-box-add-template.php', 'main', 'closed' );
    $this->Add_Option_Box ( $this->t('Association Interface'), DirName(__FILE__).'/option-box-interface.php', 'side' );
    $this->Add_Option_Box ( $this->t('Selectable Taxonomies'), DirName(__FILE__).'/option-box-taxonomies.php', 'side' );
    $this->Add_Option_Box ( $this->t('Miscellaneous'), DirName(__FILE__).'/option-box-miscellaneous.php', 'side' );
    $this->Add_Option_Box ( __('Update'), DirName(__FILE__).'/option-box-update.php', 'side' );

    // Add JavaScript to this handle
    Add_Action ('load-' . $handle, Array($this, 'Load_Options_Page'));
  }

  function Add_Option_Box($title, $include_file, $column = 'main', $state = 'opened'){
    // Check the input
    If (!Is_File($include_file)) return False;
    If ( $title == '' ) $title = '&nbsp;';

    // Column (can be 'side' or 'main')
    If ($column != '' && $column != Null && $column != 'main')
      $column = 'side';
    Else
      $column = 'main';

    // State (can be 'opened' or 'closed')
    If ($state != '' && $state != Null && $state != 'opened')
      $state = 'closed';
    Else
      $state = 'opened';

    // Add a new box
    $this->arr_option_box[$column][] = Array(
      'title' => $title,
      'file'  => $include_file,
      'state' => $state
    );
  }

  function Install_Template(){
    // Was this a Post request with data enctype?
    If (!Is_Array($_FILES)) return False;

    // Check the files
    ForEach ($_FILES AS $field_name => $arr_file){
      If (!Is_File($arr_file['tmp_name']))
        Unset ($_FILES[$field_name]);
    }

    // Check if there are uploaded files
    If (Empty($_FILES)) return False;

    // Create template dir
    If (!Is_Dir($this->template_dir) && !MkDir($this->template_dir)) return False;

    // Copy the template file
    If (IsSet($_FILES['template_zip'])){
      // Install the ZIP Template
      $zip_file = $_FILES['template_zip']['tmp_name'];
      Require_Once 'includes/file.php';
      WP_Filesystem();
      return UnZip_File ($zip_file, $this->template_dir );
    }
    ElseIf (IsSet($_FILES['template_php']) && $this->Get_Template_Properties($_FILES['template_php']['tmp_name']) ){
      // Install the PHP Template
      $php_file = $_FILES['template_php']['tmp_name'];
      $template_name = BaseName($_FILES['template_php']['name'], '.php');

      // Create dir and copy file
      If (!Is_Dir($this->template_dir . '/' . $template_name)) MkDir ($this->template_dir . '/' . $template_name);
      Copy ( $php_file, $this->template_dir . '/' . $template_name . '/' . $template_name . '.php' );

      // Copy the CSS
      If (IsSet($_FILES['template_css']) && Is_File($_FILES['template_css']['tmp_name']) ){
        Copy( $_FILES['template_css']['tmp_name'], $this->template_dir . '/' . $template_name . '/' . $template_name . '.css' );
      }
    }
    Else return False;

    // Template installed
    return True;
  }

  function Load_Options_Page(){
    // Check if the user trys to delete a template
    If (IsSet($_GET['delete']) && $this->Get_Template_Properties ($_GET['delete'])){ // You can only delete AP-Templates!
      Unlink($_GET['delete']);
      WP_Redirect( $this->Option_Page_Url(), False );
    }
    ElseIf (IsSet($_GET['delete'])){
      WP_Die('Error while deleting: ' . $_GET['delete']);
    }

    // Does the user saves options?
    $this->saved_options = $this->Save_Options();

    // Include JS
    WP_Enqueue_Script( 'dashboard' );
    WP_Enqueue_Script( 'associated-posts-options-page', $this->base_url . '/options-page.js' );

    // Include CSS
    WP_Admin_CSS( 'dashboard' );
    WP_Enqueue_Style ( 'associated-posts-options-page', $this->base_url . '/options-page.css' );
  }

  function Print_Options_Page(){
    Include DirName(__FILE__) . '/options-page.php';
  }

  function Option_Page_Url($parameter = Array(), $htmlspecialchars = True){
    $url = Add_Query_Arg($parameter, Admin_URL('options-general.php?page=' . __CLASS__));
    If ($htmlspecialchars) $url = HTMLSpecialChars($url);
    return $url;
  }

  function Add_Post_Meta_Boxes(){
    If ( $this->get_option('association_ui_type') && $this->get_option('taxonomy_selection') ){
      // Register meta boxes
      ForEach ((Array) $this->get_option('association_ui_type') AS $post_type){
        Add_Meta_Box(
          __CLASS__,
          $this->t('Associated Posts'),
          Array($this, 'Print_Meta_Box'),
          $post_type,
          'normal',
          'high'
        );
      }

      // Enqueue Meta Box Style and Scripts
      WP_Enqueue_Style( 'associated-posts-meta-box', $this->base_url . '/meta-box.css' );
      WP_Enqueue_Script( 'associated-posts-meta-box', $this->base_url . '/meta-box.js', Array('jquery') );
    }
  }

  function Print_Meta_Box(){ Include DirName(__FILE__) . '/meta-box.php'; }

  function Find_Templates(){
    $arr_template = Array_Merge (
      (Array) Glob ( DirName(__FILE__) . '/templates/*.php' ),
      (Array) Glob ( DirName(__FILE__) . '/templates/*/*.php' ),

      (Array) Glob ( Get_StyleSheet_Directory() . '/*.php' ),
      (Array) Glob ( Get_StyleSheet_Directory() . '/*/*.php' ),

      Is_Child_Theme() ? (Array) Glob ( Get_Template_Directory() . '/*.php' ) : Array(),
      Is_Child_Theme() ? (Array) Glob ( Get_Template_Directory() . '/*/*.php' ) : Array(),

      (Array) Glob ( $this->template_dir . '/*.php' ),
      (Array) Glob ( $this->template_dir . '/*/*.php' )
    );

    // Filter to add template files - you can use this filter to add template files to the user interface
    $arr_template = (Array) Apply_Filters('associated_posts_template_files', $arr_template);

    // Check if there template files
    If (Empty($arr_template)) return False;

    $arr_result = Array();
    $arr_sort = Array();
    ForEach ($arr_template AS $index => $template_file){
      // Read meta data from the template
      If (!$arr_properties = $this->Get_Template_Properties($template_file))
        Continue;
      Else
        $arr_result[RealPath($template_file)] = $arr_properties;
        $arr_sort[RealPath($template_file)] = $arr_properties['name'];
    }
    Array_MultiSort($arr_sort, $arr_result);

    return $arr_result;
  }

  function Get_Template_Properties($template_file){
    // Check if this is a file
    If (!$template_file || !Is_File ($template_file) || !Is_Readable($template_file)) return False;

    // Read meta data from the template
    $arr_properties = get_file_data ($template_file, Array(
      'name' => 'AP Template',
      'description' => 'Description',
      'author' => 'Author',
      'author_uri' => 'Author URI',
      'author_email' => 'Author E-Mail',
      'version' => 'Version'
    ));

    // Check if there is a name for this template
    If (Empty($arr_properties['name']))
      return False;
    Else
      return $arr_properties;
  }

  function Get_Default_Template(){
    // Which file set the user as default?
    $template_file = $this->get_option('default_template_file');
    If (Is_File($template_file)) return $template_file;

    // Is there a template by the theme author
    $template_file = RealPath(Get_Query_Template( 'associated-posts' ));
    If (Is_File($template_file)) return $template_file;

    // Else:
    return RealPath(DirName(__FILE__) . '/templates/title-excerpt-thumbnail.php');
  }

  function Get_Post_Types(){
    return get_post_types(Array(
      'show_ui' => True
    ), 'objects');
  }

  function Get_Taxonomies ($post_type){
    $arr_taxonomy = get_taxonomies (Array(
      #'object_type' => Array($post_type),
      #'public' => True,
      #'show_ui' => True
    ), 'objects', 'and');

    ForEach ($arr_taxonomy AS $index => $taxonomy){
      If (!In_Array($post_type, $taxonomy->object_type))
        Unset ($arr_taxonomy[$index]);
    }

    return $arr_taxonomy;
  }

  function Get_Authors(){
    $arr_author = Array();

    If (function_Exists('get_users')){ /* WP 3.1 and higher */
      ForEach ( (Array) get_users(Array('who' => 'author')) AS $user)
        $arr_author[] = get_userdata ($user->ID);
    }
    Else {
      ForEach ( (Array) get_author_user_ids() AS $author_id)
        $arr_author[] = get_userdata( $author_id );
    }

    return $arr_author;
  }

  function Get_All_Posts($post_type, $exclude = Array()){
    $post_query = new WP_Query(Array(
      'post_type' => $post_type,
      'posts_per_page' => -1,
      'post_status' => 'publish',
      'caller_get_posts' => 1, // for WP < 3.1
      'ignore_sticky_posts' => 1,
      'post__not_in' => (Array) $exclude,
      'cache_results' => false
    ));

    return $post_query->posts;
  }

  function Field_Name($option_name){
    // Generates field names for the meta box
    return __CLASS__ . '[' . $option_name . ']';
  }

  function Save_Meta_Box($post_id){
    // If this is an autosave we dont care
    If ( Defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    // Check if this request came from the edit page section
    If (IsSet($_POST[ __CLASS__ ]))
      // Save Meta data
      update_post_meta ($post_id, '_' . __CLASS__, (Array) ($_POST[ __CLASS__ ]) );
  }

  function Add_Templates_Styles(){
    /* JOE KENDALL MOD WTF WOULD I WANT TO DO THIS FOR?
    // Find the template
    #$association_data = $this->get_association_data();
    #$template_file = $association_data['template'];
    #If (!Is_File($template_file)) $template_file = $this->get_default_template();

    $arr_template_files = $this->Find_Templates();
    ForEach ($arr_template_files AS $template_file => $template_details){
      // If there is no style sheet we bail out
      If (!Is_File(DirName($template_file) . '/' . BaseName($template_file, '.php') . '.css')) Continue;

      // Locate the URL of the style sheet
      $style_sheet = get_bloginfo('wpurl') . '/' .
                     Str_Replace("\\", '/', SubStr(RealPath(DirName($template_file)), Strlen(ABSPATH))) . '/' .
                     BaseName($template_file, '.php') . '.css';

      // run the filter for the template file
      $style_sheet = Apply_Filters('associated_posts_style_sheet', $style_sheet);

      // Print the stylesheet link
      If ($style_sheet) WP_Enqueue_Style ( 'associated-posts-' . Sanitize_Title(BaseName($template_file, '.php')), $style_sheet );
    }
    */
  }

  function ShortCode($attr = Null){
    Global $post;

    // Check the Singular Mode
    If ($this->get_option('show_only_on_singulars'))
      If (!Is_Page($post->ID) && !Is_Single($post->ID))
        return False;

    // Render the posts
    return $this->render_associated_posts();
  }

  function Render_Associated_Posts(){
    // Get the association settings
    If (!IsSet($GLOBALS['post']->associated_posts))
      return False;
    Else
      $meta = $GLOBALS['post']->associated_posts;

    // Uses template filter
    $template_file = Apply_Filters('associated_posts_template', $meta['template']);

    // If there is no valid template file we bail out
    If (!Is_File($template_file)) $template_file = $this->get_default_template();

    // Cache the current post
    Array_Push($this->post_object_cache, $GLOBALS['post']);

    // Include the template
    Ob_Start();
    Include $template_file;
    $result = Ob_Get_Contents();
    Ob_End_Clean();

    // Restore post data
    If (!Empty($this->post_object_cache)){
      $GLOBALS['post'] = Array_Pop($this->post_object_cache);
      Setup_PostData($GLOBALS['post']);
    }

    // return code
    return $result;
  }

  function Get_Association_Data($post_id = Null){
    // Get the post id
    If ($post_id == Null && Is_Object($GLOBALS['post']))
      $post_id = $GLOBALS['post']->ID;
    ElseIf ($post_id == Null && !Is_Object($GLOBALS['post']))
      return False;

    // Read meta data
    $arr_meta = get_post_meta($post_id, '_' . __CLASS__, True);
    If (Empty($arr_meta)) $arr_meta = get_post_meta($post_id, '_wp_plugin_associated_posts_pro', True); // backward compatibility
    If (Empty($arr_meta)) $arr_meta = $this->Get_PPA_Data($post_id); // more backward compatibility
    If (Empty($arr_meta) || !Is_Array($arr_meta)) return False;

    // Get post ids
    $arr_meta['post_ids'] = (Array) $this->get_associated_post_ids($arr_meta);
    $arr_posts_without_stickies = Array_Diff($arr_meta['post_ids'], Get_Option('sticky_posts', Array()));

    // Number of pages
    If (IntVal($arr_meta['posts_per_page']) > 0)
      $arr_meta['numpages'] = Ceil((Count($arr_posts_without_stickies) - IntVal ($arr_meta['offset'])) / IntVal($arr_meta['posts_per_page']));
    Else
      $arr_meta['numpages'] = 1;

    // Return
    return $arr_meta;
  }

  function Get_PPA_Data($post_id){
    $arr_meta = Array_Merge(
      Array(
        'category_select_mode' => 'or_one',
        'category' => Array(),
        'tag_select_mode' => 'or_one',
        'tag' => Array(),
        'author_select_mode' => 'or',
        'author' => Array(),
        'post' => Array(),
        'post_limit' => '',
        'order_by' => 'date',
        'order' => 'ASC'
      ),
      (Array) get_post_meta($post_id, '_wp_plugin_associate_posts_and_pages', True), // Deprecated
      (Array) get_post_meta($post_id, '_wp_plugin_post_page_associator', True)
    );

    // Translate these data
    $pro_meta = Array();

    If ($arr_meta['category_select_mode'] == 'or_one') $pro_meta['category']['mode'] = 'add_or';
    If ($arr_meta['category_select_mode'] == 'and_one') $pro_meta['category']['mode'] = 'add_and';
    If ($arr_meta['category_select_mode'] == 'or_all') $pro_meta['category']['mode'] = 'filter_or';
    If ($arr_meta['category_select_mode'] == 'and_all') $pro_meta['category']['mode'] = 'filter_and';
    If (!Empty($arr_meta['category'])) $pro_meta['category']['selection'] = (Array) $arr_meta['category'];

    If ($arr_meta['tag_select_mode'] == 'or_one') $pro_meta['post_tag']['mode'] = 'add_or';
    If ($arr_meta['tag_select_mode'] == 'and_one') $pro_meta['post_tag']['mode'] = 'add_and';
    If ($arr_meta['tag_select_mode'] == 'or_all') $pro_meta['post_tag']['mode'] = 'filter_or';
    If ($arr_meta['tag_select_mode'] == 'and_all') $pro_meta['post_tag']['mode'] = 'filter_and';
    If (!Empty($arr_meta['tag'])) $pro_meta['post_tag']['selection'] = (Array) $arr_meta['tag'];

    If ($arr_meta['author_select_mode'] == 'or') $pro_meta['_wp_user']['mode'] = 'add';
    If ($arr_meta['author_select_mode'] == 'and') $pro_meta['_wp_user']['mode'] = 'filter';
    If (!Empty($arr_meta['author'])) $pro_meta['_wp_user']['selection'] = (Array) $arr_meta['author'];

    If (!Empty($arr_meta['post'])) $pro_meta['_explicitly']['selection'] = (Array) $arr_meta['post'];

    $arr_meta = Array(
      'post_selection' => Array(
        'post' => $pro_meta
      )
    );

    #If (!Empty($arr_meta['offset'])) $pro_meta['offset'] = $arr_meta['offset'];
    #If (!Empty($arr_meta['order_by'])) $pro_meta['order_by'] = $arr_meta['order_by'];
    #If (!Empty($arr_meta['order'])) $pro_meta['order'] = $arr_meta['order'];
    $arr_meta['template'] = $this->Get_Default_Template();

    return $arr_meta;
  }

  function Get_Associated_Post_Ids ($meta_data){
    If (Empty($meta_data) || !Is_Array($meta_data) || Empty($meta_data['post_selection'])) return False;

    // Prepare result Array
    $arr_result_post_ids = Array();

    // Select the posts
    ForEach ( (Array) $meta_data['post_selection'] AS $post_type => $arr_post_type){
      // Check if the post type exists
      If (!post_type_exists($post_type)) Continue;

      // Prepare Selection Array
      $arr_selection = Array();

      // Prepare Filter Array
      $arr_filter = False; // This var will become an array if there was set a filter.

      // Prepare the explicit Post Selection Array
      $arr_explicit = Array();

      // Handle the post selections
      ForEach ( $arr_post_type AS $taxonomy_name => $arr_taxonomy_selection){
        $mode = IsSet($arr_taxonomy_selection['mode']) ? $arr_taxonomy_selection['mode'] : False;
        If (!$mode && $taxonomy_name != '_explicitly') Continue;

        $selection = IsSet($arr_taxonomy_selection['selection']) ? (Array) $arr_taxonomy_selection['selection'] : Array();
        If (Empty($selection)) Continue;

        #Echo '<p>' . $post_type . ' | ' . $taxonomy_name . ' | ' . $mode . ' | ' . Join(', ', $selection) . '</p>';

        If ($taxonomy_name == '_wp_user' && $mode == 'add'){
          // Add all posts written by these users
          $arr_selection = Array_Merge ($arr_selection, $this->get_posts_by_author($post_type, (Array) $selection));
        }

        ElseIf ($taxonomy_name == '_wp_user' && $mode == 'filter'){
          // Filter all posts written by these users
          If (!$arr_filter)
            $arr_filter = $this->get_posts_by_author($post_type, (Array) $selection);
          Else
            $arr_filter = Array_Intersect ($arr_filter, $this->get_posts_by_author($post_type, (Array) $selection));
        }

        ElseIf ($taxonomy_name == '_explicitly'){
          // Explicit choosen posts
          $arr_explicit = (Array) $selection;
        }

        ElseIf ($mode == 'add_or' && Taxonomy_Exists($taxonomy_name)){
          $arr_selection = Array_Merge ($arr_selection, $this->get_posts_by_term_or($post_type, $taxonomy_name, (Array) $selection));
        }

        ElseIf ($mode == 'filter_or' && Taxonomy_Exists($taxonomy_name)){
          If (!Is_Array($arr_filter))
            $arr_filter = $this->get_posts_by_term_or($post_type, $taxonomy_name, (Array) $selection);
          Else
            $arr_filter = Array_Intersect ($arr_filter, $this->get_posts_by_term_or($post_type, $taxonomy_name, (Array) $selection));
        }

        ElseIf ($mode == 'add_and' && Taxonomy_Exists($taxonomy_name)){
          $arr_selection = Array_Merge ($arr_selection, $this->get_posts_by_term_and($post_type, $taxonomy_name, (Array) $selection));
        }

        ElseIf ($mode == 'filter_and' && Taxonomy_Exists($taxonomy_name)){
          If (!$arr_filter)
            $arr_filter = $this->get_posts_by_term_and($post_type, $taxonomy_name, (Array) $selection);
          Else
            $arr_filter = Array_Intersect ($arr_filter, $this->get_posts_by_term_and($post_type, $taxonomy_name, (Array) $selection));
        }
      }

      // Run the filter
      If (Is_Array($arr_filter)) $arr_selection = Array_Intersect ($arr_selection, $arr_filter);

      // Add the explicitly choosen posts
      $arr_result_post_ids = Array_Merge ($arr_result_post_ids, $arr_selection, $arr_explicit);
    }

    // There are no posts we have to care about
    If (Empty($arr_result_post_ids)) return False;
    Else $arr_result_post_ids = Array_Unique ($arr_result_post_ids);

    return $arr_result_post_ids;
  }

  function Get_Query_Vars($association_data, $loop_protection = True){
    If (!Is_Array($association_data) || Empty($association_data)) return False;
    If (Empty($association_data['post_ids'])) return False;

    // Loop protection -- Exclude posts which are already shown in this thread
    If ($loop_protection){
      $arr_thread_post_ids = Array();
      ForEach ($this->post_object_cache AS $p) $arr_thread_post_ids[] = $p->ID;
      $association_data['post_ids'] = Array_Diff ($association_data['post_ids'], $arr_thread_post_ids);
      If (Empty($association_data['post_ids'])) return False;
    }

    // Filter sticky posts
    $arr_sticky_post_ids = Get_Option('sticky_posts', Array()); #Print_R ($arr_sticky_post_ids);
    $post__in = Array_Diff($association_data['post_ids'], $arr_sticky_post_ids); #Print_R ($post__in);
    $post__not_in = Array_Diff($arr_sticky_post_ids, $association_data['post_ids']); #Print_R ($post__not_in);

    // Posts per page
    If (Empty($association_data['posts_per_page']))
      $posts_per_page = -1; //Count($association_data['post_ids']);
    Else
      $posts_per_page = IntVal($association_data['posts_per_page']);

    // Number of pages
    $number_of_pages = ($posts_per_page > 0) ? (Ceil(Count($post__in) / $posts_per_page)) : 1;

    // Current page
    $current_page = IntVal (Get_Query_Var( 'page' ));
    If ($current_page < 1) $current_page = 1;
    If ($current_page > $number_of_pages) $current_page = $number_of_pages;

    // Offset
    $offset = IntVal($association_data['offset']);

    // Order by meta value
    If ($association_data['order_by'] != 'meta_value' && $association_data['order_by'] != 'meta_value_num')
      Unset ($association_data['meta_key']);

    // Result
    return Array (
      'post__in' => $post__in,
      'post__not_in' => $post__not_in,
      'posts_per_page' => $posts_per_page,
      'paged' => $current_page,
      'offset' => $offset,
      'post_type' => 'any',
      'orderby' => $association_data['order_by'],
      'meta_key' => IsSet($association_data['meta_key']) ? $association_data['meta_key'] : False,
      'order' => $association_data['order'],
      #'suppress_filters' => True,
      'cache_results' => False,
      'tb' => True // We pretend this is a trackbackquery to avoid filters of exclude plugins
    );
  }

  function Get_Associated_Posts($post_id = False){
    If ($post_id){
      $association_data = $this->get_association_data($post_id);
    }
    ElseIf (IsSet($GLOBALS['post'])) {
      $association_data = $GLOBALS['post']->associated_posts;
    }
    Else Return False;

    If ($query_vars = $this->get_query_vars($association_data)){
			Add_Filter ('posts_results', Array($this, 'Filter_Posts_Result'), 999, 2);
			$query = New WP_Query($query_vars);
			Remove_Filter ('posts_results', Array($this, 'Filter_Posts_Result'), 999, 2);

			// return WP_Query object
			return $query;
    }
    Else Return False;
  }

  function Filter_Posts_Result($posts, &$wp_query){
		$wp_query->is_home = True;
		return $posts;
	}

  function Get_Posts_By_Term_Or ($post_type, $taxonomy, $arr_term_id){
    $arr_post_id = $GLOBALS['wpdb']->get_col('
    SELECT
    posts.ID

    FROM
    '.$GLOBALS['wpdb']->posts.' posts,
    '.$GLOBALS['wpdb']->term_relationships.' term_relationships,
    '.$GLOBALS['wpdb']->term_taxonomy.' term_taxonomy

    WHERE
    posts.post_status = "publish" AND
    posts.post_type = "'.$post_type.'" AND
    posts.ID = term_relationships.object_id AND
    term_relationships.term_taxonomy_id = term_taxonomy.term_taxonomy_id AND
    term_taxonomy.taxonomy = "'.$taxonomy.'" AND
    term_taxonomy.term_id IN ('.Implode(', ', (Array) $arr_term_id).')

    GROUP BY
    posts.ID
    ');

    return $arr_post_id;
  }

  function Get_Posts_By_Term_And ($post_type, $taxonomy, $arr_term_id){
    $arr_post_id = Array();
    ForEach ( (Array) $arr_term_id AS $term_id ){
      // Read the posts
      If (Empty($arr_post_id))
        $arr_post_id = $this->get_posts_by_term_or ($post_type, $taxonomy, $term_id);
      Else
        $arr_post_id = Array_Intersect ( (Array) $arr_post_id, $this->get_posts_by_term_or ($post_type, $taxonomy, $term_id) );

      // Check if the new array is empty
      If (Empty($arr_post_id)) return Array();
    }

    return $arr_post_id;
  }

  function Get_Posts_By_Author ($post_type, $arr_user_id){
    $arr_post_id = $GLOBALS['wpdb']->get_col('
    SELECT
    posts.ID

    FROM
    '.$GLOBALS['wpdb']->posts.' posts

    WHERE
    posts.post_status = "publish" AND
    posts.post_type = "'.$post_type.'" AND
    posts.post_author IN ('.Implode(', ', (Array) $arr_user_id).')

    GROUP BY
    posts.ID
    ');

    return $arr_post_id;
  }

  function The_Post($post){
    // Set the associated posts property
    If (!IsSet($post->associated_posts))
      If (!$post->associated_posts = $this->get_association_data())
        return False;

    // Set the number of pages
    If (!$post->associated_posts['disable_pagination'] && $GLOBALS['numpages'] < $post->associated_posts['numpages']){
      $GLOBALS['multipage'] = True;
      $GLOBALS['numpages'] = $post->associated_posts['numpages'];
    }
  }

  function Add_Page_Navigation($content){
		$wp_link_pages = WP_Link_Pages(Array(
			'echo' => False,
			'pagelink' => '<span class="page-number">%</span>',
			'before' => '<div class="pagination">',
			'after' => '</div>'
		));
		return $content . $wp_link_pages ;
	}

  function Get_Term_Path($term, $separator = ' &raquo; '){
    $arr_path = Array( $term->name );
    While ($term->parent != 0){
      $term = get_term($term->parent, $term->taxonomy);
      Array_UnShift ($arr_path, $term->name);
    }
    return Join($arr_path, $separator);
  }

  function Set_Current_Page($content){
    // Set the current page
    $GLOBALS['page'] = get_query_var('page') ? IntVal(get_query_var('page')) : 1;

    // No modified content
    return $content;
  }

  function Filter_Content($content){
    // Append the ShortCode to the Content
    $content = Str_Replace('[associated-posts', '[associated_posts', $content);
    If ( StrPos($content, '[associated_posts]') === False && // Avoid double inclusion of the ShortCode
         StrPos($content, '[associated_posts ') === False && // Without closing bracket to find ShortCodes with attributes
         Apply_Filters('associated_posts_auto_append', True) && // You can use this filter to control the auto append feature
         $this->get_option('posts_position') != 'none' && // User can disable the auto append feature
         !post_password_required() // The user isn't allowed to read this post
       ){

      // Add the ShortCode to the current content
      If ($this->get_option('posts_position') == 'top')
        Return '[associated_posts] ' . $content;
      ElseIf ($this->get_option('posts_position') == 'bottom')
        Return Trim($content) . ' [associated_posts]';

    }
    Else
      // do not include the Shortcode in the content
      Return $content;
  }

  function Get_Post_Thumbnail($post_id = Null, $size = 'thumbnail'){
    /* Return Value: An array containing:
         $image[0] => attachment id
         $image[1] => url
         $image[2] => width
         $image[3] => height
    */
    If ($post_id == Null) $post_id = get_the_id();

    If (function_Exists('get_post_thumbnail_id') && $thumb_id = get_post_thumbnail_id($post_id) )
      return Array_Merge ( Array($thumb_id), (Array) wp_get_attachment_image_src($thumb_id, $size) );
    ElseIf ($arr_thumb = $this->get_post_attached_image($post_id, 1, 'rand', $size))
      return $arr_thumb[0];
    Else
      return False;
  }

  function Get_Post_Attached_Image($post_id = Null, $number = 1, $orderby = 'rand', $image_size = 'thumbnail'){
    If ($post_id == Null) $post_id = get_the_id();
    $number = IntVal ($number);
    $arr_attachment = get_posts (Array( 'post_parent'    => $post_id,
                                        'post_type'      => 'attachment',
                                        'numberposts'    => $number,
                                        'post_mime_type' => 'image',
                                        'orderby'        => $orderby ));

    // Check if there are attachments
    If (Empty($arr_attachment)) return False;

    // Convert the attachment objects to urls
    ForEach ($arr_attachment AS $index => $attachment){
      $arr_attachment[$index] = Array_Merge ( Array($attachment->ID), (Array) wp_get_attachment_image_src($attachment->ID, $image_size));
      /* Return Value: An array containing:
           $image[0] => attachment id
           $image[1] => url
           $image[2] => width
           $image[3] => height
      */
    }

    return $arr_attachment;
  }

  function Check_For_Update($value){
    // Check if the update function is enabled
    If ($this->get_option('disable_update_notification')) return $value;

    If (!function_Exists('Get_Plugins')) return $value;

    // Find this plugin
    $found_plugin = False;
    ForEach ( (Array) Get_Plugins() AS $file => $data ){
      If (Str_Replace('\\', '/', SubStr(__FILE__, -1*StrLen($file))) == $file){
        $plugin_file = $file;
        $plugin_data = $data;
        $found_plugin = True;
        Break;
      }
    }
    If (!$found_plugin) return $value;

    // Get the current version here
    $local_version = $plugin_data['Version'];
    $plugin_uri = Add_Query_Arg(Array('format' => 'serialized'), $plugin_data['PluginURI']);

    // Check for the current online version
    $remote_page = Unserialize(@File_Get_Contents($plugin_uri));

    // Compare versions
    If (Version_Compare($local_version, $remote_page->version, '<')){
      $plugin = New stdClass;
      $plugin->id = $remote_page->id;
      $plugin->slug = __CLASS__;
      $plugin->new_version = $remote_page->version;
      $plugin->url = $remote_page->url;
      $plugin->package = SPrintF($remote_page->download, $this->get_option('update_username'), $this->get_option('update_password'));
      $value->response[$plugin_file] = $plugin;
    }

    // Return the filter input
    return $value;
  }

  function plugins_api($false, $action, $args){
    If ($action == 'plugin_information' && $args->slug == __CLASS__){
      WP_Enqueue_Style('plugin-details', $this->base_url . '/plugin-details.css' );
      $plugin_data = get_plugin_data(__FILE__);
      $plugin_data = @Unserialize(@File_Get_Contents(Add_Query_Arg(Array('format' => 'serialized', 'locale' => get_locale()), $plugin_data['PluginURI'])));
      #Print_R ($plugin_data);
      $plugin = New stdClass;
      $plugin->name = $plugin_data->name;
      $plugin->slug = __CLASS__;
      $plugin->version = $plugin_data->version;
      $plugin->author = SPrintF('<a href="%1$s">%2$s</a>', $plugin_data->author->url, $plugin_data->author->display_name);
      $plugin->author_profile = $plugin_data->author->url;
      $plugin->contributors = Array( 'dhoppe' => $plugin_data->author->url );
      $plugin->requires = '3.3';
      $plugin->rating = Round(Rand(90, 100));
      $plugin->num_ratings = Round( (Time()-1262300400) / (3*24*60*60));
      $plugin->downloaded = Round( (Time()-1262300400) / (60*60) );
      #$plugin->last_updated = Date('Y-m-d', FileMTime(__FILE__));
      $plugin->homepage = $plugin_data->url;
      $plugin->download_link = SPrintF($plugin_data->download, $this->get_option('update_username'), $this->get_option('update_password'));
      $plugin->sections = Is_Array($plugin_data->content) ? $plugin_data->content : Array( __('Description') => (String) $plugin_data->content );
      $plugin->external = True;
      return $plugin;
    }
    Else return $false;
  }

  function Clear_Plugin_Update_Cache(){
    Update_Option('_site_transient_update_plugins', Array());
  }

} /* End of Class */
New wp_plugin_associated_posts;
} /* End of If-Class-Exists-Condition */


// Associated Posts Widget
If (!Class_Exists('wp_widget_associated_posts')){
Class wp_widget_associated_posts Extends WP_Widget {
  var $base_url;
  var $arr_option;
  var $AP;

  function __construct( $id_base = False, $name = False, $widget_options = Array(), $control_options = Array() ){
    // Catch the Plugin
    $this->AP = $GLOBALS['wp_plugin_associated_posts'];

    // Register Widget
    // Setup the Widget data
    parent::__construct (
      False,
      $this->t('Associated Posts'),
      Array('description' => $this->t('Displays the associated posts of the current post in the sidebar.'))
    );
    Add_Action('init', Array($this, 'Register_Widget'));

    // Read base_url
    $this->base_url = $this->AP->base_url;

    // Hooks
    Add_Action ('wp_print_styles', Array($this, 'Add_Template_Style'));
  }

  function Register_Widget(){
    // Setup the Widget data
    $this->name = $this->t('Associated Posts');
    $this->widget_options['description'] = $this->t('Displays the associated posts of the current post in the sidebar.');
  }

  function t ($text, $context = ''){
    // Translates the string $text with context $context
    return $this->AP->t($text, $context);
  }

  function Default_Options(){
    // Default settings
    return Array(
      'title' => $this->t('Associated Posts'),
      'template_file' => Is_Object($this->AP) ? $this->AP->Get_Default_Template() : False
    );
  }

  function Load_Options($options){
    // Prepare $options array
    $options = (ARRAY) $options;

    // Delete empty values
    ForEach ($options AS $key => $value)
      If (!$value) Unset($options[$key]);

    // Load options
    $this->arr_option = Array_Merge ($this->Default_Options(), $options);
  }

  function Get_Option($key, $default = False){
    If (IsSet($this->arr_option[$key]) && $this->arr_option[$key])
      return $this->arr_option[$key];
    Else
      return $default;
  }

  function Set_Option($key, $value){
    $this->arr_option[$key] = $value;
  }

  function Add_Template_Style(){
    /* JOE KENDALL MOD. WTF WOULD I WANT TO DO THIS FOR?
    // Set the template file
    $template_file = $this->get_option('template_file');
    If (!Is_File($template_file)) return False;

    // If there is no style sheet we bail out
    If (!Is_File(DirName($template_file) . '/' . BaseName($template_file, '.php') . '.css')) return False;

    // Locate the URL of the style sheet
    $style_sheet = get_bloginfo('wpurl') . '/' .
                   Str_Replace("\\", '/', SubStr(RealPath(DirName($template_file)), Strlen(ABSPATH))) . '/' .
                   BaseName($template_file, '.php') . '.css';

    // run the filter for the template file
    $style_sheet = Apply_Filters('associated_posts_widget_style_sheet', $style_sheet);

    // Print the stylesheet link
    If ($style_sheet) WP_Enqueue_Style ( 'associated-posts-widget', $style_sheet );
    */
  }

  function Widget ($args, $options){
		Global $post;

    // Load options
    $this->Load_Options ($options); Unset ($options);

    If (!IsSet($post->associated_posts))
      If (!$post->associated_posts = $this->AP->get_association_data())
        return False;

    // Check if there are posts
    If (!IsSet($post->associated_posts)) return False;
    If (Empty($post->associated_posts)) return False;
    If (!IsSet($post->associated_posts['post_ids'])) return False;
    If (Empty($post->associated_posts['post_ids'])) return False;

    // Set the template file
    $template_file = $post->associated_posts['template'];
    $post->associated_posts['template'] = $this->get_option('template_file');

    // Print the Widget
    Echo $args['before_widget'] . $args['before_title'] . Apply_Filters('widget_title', $this->get_option('title')) . $args['after_title'];
    If (Is_Object($this->AP)) Echo $this->AP->Render_Associated_Posts();
    Echo $args['after_widget'];

    // Reset the template file
    $post->associated_posts['template'] = $template_file;
  }

  function Form ($options){
    // Load options
    $this->Load_Options ($options); Unset ($options);

    // Show form
    If (!Is_Object($this->AP)) : ?>
      <p class="warning">
        <?php Echo $this->t('Please activate the Associated Posts Plugin!'); ?>
      </p>

    <?php Else : ?>
      <p>
        <label for="<?php Echo $this->get_field_id('title')?>"><?php Echo $this->t('Title') ?></label>:
        <input type="text" name="<?php Echo $this->get_field_name('title')?>" id="<?php Echo $this->get_field_id('title')?>" value="<?php Echo $this->get_option('title')?>" />
      </p>

      <h3><?php Echo $this->t('Template') ?></h3>
      <?php ForEach ($this->AP->Find_Templates() AS $template_file => $arr_template) : ?>
      <p>
        <input type="radio" name="<?php Echo $this->get_field_name('template_file')?>" id="<?php Echo $this->get_field_id('template_file')?>_<?php Echo Sanitize_Title($template_file) ?>" value="<?php Echo $template_file ?>" <?php Checked($this->get_option('template_file'), $template_file)?> />
        <label for="<?php Echo $this->get_field_id('template_file')?>_<?php Echo Sanitize_Title($template_file) ?>"><?php Echo $arr_template['name'] ?></label> <small>(<em><?php Echo $arr_template['description'] ?></em>)</small>
      </p>
    <?php EndForEach; ?>

    <?php EndIf;
  }

  function Update ($new_settings, $old_settings){
    return $new_settings;
  }

} /* End of Class */
Add_Action ('widgets_init', Create_function ('','Register_Widget(\'wp_widget_associated_posts\');') );
} /* End of If-Class-Exists-Condition */
/* End of File */
