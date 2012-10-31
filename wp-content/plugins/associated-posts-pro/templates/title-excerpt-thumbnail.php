<?php


/*
AP Template: Title, Thumbnail and Excerpt
Description: This template shows the title, an excerpt and a thumbnail for each associated post. Feel free to use it to create your own customization. You can find this template in the plugin folder.
Version: 1.0
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/


If ( $association_query = $this->get_associated_posts() ) : ?>
  <div class="associated-posts <?php Echo Sanitize_Title(BaseName(__FILE__, '.php')) ?>">  
  <?php While ($association_query->have_posts()) : $association_query->the_post(); ?>
  <div class="associated-post">
    <h3 class="post-title">
     <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title() ?></a>
    </h3>
      
    <?php If ( $thumb = $this->get_post_thumbnail(get_the_id()) ) : ?>
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
    <?php EndIf; ?>
    
    <div class="post-excerpt"><?php the_excerpt() ?></div>
    
    <?php If ($thumb) : // In 99% of all cases we only need the clearer if we used a thumbnail image. ?>
    <div class="clear"></div>
    <?php EndIf; ?>
  </div>
  <?php EndWhile; ?>  
  </div>
<?php EndIf;
/* End of File */