  <ul> <?php
    foreach ( $rss_items as $item ) { ?>
      <li>
        <a target="_blank"  href="<?php echo $item->get_permalink();?>"><?php echo $item->get_title(); ?></a>
      </li> <?php
    } ?>
  </ul>