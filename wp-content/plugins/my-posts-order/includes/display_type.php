<?php
  if ( isset($rss_items) && count($rss_items) > 0) {
    echo $before_widget;
    if ( $title ) echo $before_title . $title . $after_title;
    if (file_exists(MPO_CUSTOM_TEMPLATE . 'loop_rss.php') && $instance['display_type'] == 3) {
      require MPO_CUSTOM_TEMPLATE . 'loop_rss.php';
    } else {
      require 'loop_rss.php';
    }
    echo $after_widget;
  } else {
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) {
     echo $before_widget;
     if ( $title ) echo $before_title . $title . $after_title;
      switch ($instance['display_type']) {
        case 1:
          require 'loop.php';
        break;
        case 2: ?>
          <select onchange='document.location.href=this.options[this.selectedIndex].value;' name="<?php echo $this->get_field_name("mpo_sel_box"); ?>" >
            <option value="">Select Post</option><?php
            while ($the_query->have_posts()) : $the_query->the_post(); ?>
              <option value="<?php the_permalink(); ?>"><?php the_title(); ?></option> <?php
            endwhile; ?>
          </select> <?php
        break;
        case 3:
          if (file_exists(MPO_CUSTOM_TEMPLATE . 'loop.php') ) {
            require MPO_CUSTOM_TEMPLATE . 'loop.php';
          } else {
            require 'loop.php';
          }
        break;
      }
       echo $after_widget;
       // Reset the global $the_post as this query will have stomped on it
       wp_reset_postdata();
    } else {
      echo 'No Post Found';
    }
  }