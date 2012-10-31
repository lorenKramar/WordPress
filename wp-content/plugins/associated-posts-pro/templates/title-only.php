<?php


/*
AP Template: Post Title List
Description: This template shows the associated post as unordered list. (Titles only)
Version: 1.0
Author: Dennis Hoppe
Author URI: http://DennisHoppe.de
*/

If ( $association_query = $this->get_associated_posts() ) : ?>

  <ul class="associated-posts <?php Echo Sanitize_Title(BaseName(__FILE__, '.php')) ?>">  
  <?php While ($association_query->have_posts()) : $association_query->the_post(); ?>
  <li class="associated-post">
    <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title() ?></a>
  </li>
  <?php EndWhile; ?>
  </ul>
  
<?php EndIf;
/* End of File */
