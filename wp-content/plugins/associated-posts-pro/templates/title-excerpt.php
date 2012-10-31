<?php


/*
AP Template: Title and Excerpt
Description: This template shows the title and an excerpt for each associated post.
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
    
    <div class="post-excerpt"><?php the_excerpt() ?></div>

  </div>
  <?php EndWhile; ?>
  </div>
<?php EndIf;
/* End of File */