
<?php
/*
Plugin Name: Recent Party Pirates Posts
Description: Random Post Widget grabs a random post and the associated thumbnail to display on your sidebar
Author: 
Version: 1
Author URI: 
*/
 
 
class RecentPartyPiratePost extends WP_Widget
{
  function RecentPartyPiratePost()
  {
    $widget_ops = array('classname' => 'RecentPartyPiratePost', 'description' => 'Displays Recent Party Pirates Posts' );
    $this->WP_Widget('RecentPartyPiratePost', 'Recent Party Pirate Post', $widget_ops);
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
    

      $args = array( 'numberposts' => '10', 'post_type' => 'party_pirates' );
      $recent_posts = wp_get_recent_posts( $args );
      foreach( $recent_posts as $recent ){
        echo '<div class="recent_post"><h2><a href="' . get_permalink($recent["ID"]) . '" title="Look '.esc_attr($recent["post_title"]).'" >' .   $recent["post_title"].'</a> </h2> ' . '<div class="excerpt">' . '&ldquo;' . $recent["post_excerpt"] . '&rdquo;' .'</div></div>';
      }

 
    echo $after_widget;
  }
 
}
add_action( 'widgets_init', create_function('', 'return register_widget("RecentPartyPiratePost");') );?>