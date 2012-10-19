
<?php
/*
Plugin Name: Recent Posts - ALL post types
Description: Displays ALL recent post types
Author: 
Version: 1
Author URI: 
*/
 
 
class AllRecentPosts extends WP_Widget
{
  function AllRecentPosts()
  {
    $widget_ops = array('classname' => 'AllRecentPosts', 'description' => 'Displays all recent post types' );
    $this->WP_Widget('AllRecentPosts', 'All Recent Post Types', $widget_ops);
  }
 
  function form($instance)
  {
    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    $title = $instance['title'];
?>
  <p><label for="<?php echo $this->get_field_id('title'); ?>">Title: <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($title); ?>" /></label></p>
<?php
  }
 
  function update($new_instance, $old_instance)
  {
    $instance = $old_instance;
    $instance['title'] = $new_instance['title'];
    return $instance;
  }
 
  function widget($args, $instance)
  {
    extract($args, EXTR_SKIP);
 
    echo $before_widget;
    $title = empty($instance['title']) ? ' ' : apply_filters('widget_title', $instance['title']);
 
    if (!empty($title))
      echo $before_title . $title . $after_title;;
 
    // WIDGET CODE GOES HERE
    

      $args = array( 'numberposts' => '5', 'post_type' => array( 'party_pirates', 'megaziner', 'post', 'collectible') );
      $recent_posts = wp_get_recent_posts( $args );
      echo '<ul>';
      foreach( $recent_posts as $recent ){
        echo '<li class="recent_post"><h2><a href="' . get_permalink($recent["ID"]) . '" title="Look '.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </h2> ';
        if($recent["post_excerpt"] !== '') {
          '<p class="excerpt">' .  $recent["post_excerpt"] . '</p>';
        }
        echo '</li>';
      }
      echo '</ul>';

    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("AllRecentPosts");') );?>