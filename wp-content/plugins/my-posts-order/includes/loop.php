  <ul>  <?php
    while ($the_query->have_posts()) : $the_query->the_post(); ?>
      <li>
        <span class="mpo_post_title">
          <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
            <?php if ( get_the_title() ) the_title(); else the_ID(); ?>
          </a>
        </span>
      </li> <?php
    endwhile; ?>
  </ul>