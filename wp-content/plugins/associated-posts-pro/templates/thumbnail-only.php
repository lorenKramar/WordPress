<?php


/*
AP Template: Thumbnails Only
Description: This template shows only a thumbnail for each associated post.
Version: 1.0
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/


If ( $association_query = $this->get_associated_posts() ) : ?>
  <div class="associated-posts <?php Echo Sanitize_Title(BaseName(__FILE__, '.php')) ?>">  
  <?php
  While ($association_query->have_posts()) : $association_query->the_post();
  If ( !$thumb = $this->get_post_thumbnail(get_the_id()) ) Continue; ?>
  
  <div class="associated-post">
    
    <div class="thumb-frame">
      <a href="<?php the_permalink(); ?>" title="<?php the_title() ?>">
        <img src="<?php Echo $thumb[1] ?>"
             width="<?php Echo $thumb[2] ?>"
             height="<?php Echo $thumb[3] ?>"
             alt="<?php the_title() ?>"
             title="<?php the_title() ?>"
             class="thumb post-preview-image alignleft" />
      </a>
    </div>
    
  </div>
  <?php EndWhile; ?>
  <div class="clear"></div>
  </div>
<?php EndIf;
/* End of File */