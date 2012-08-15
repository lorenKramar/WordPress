<table id="table_entries" class="w100">
  <tbody id="tbody_entries"> <?php
    $row_count = 0;
    $background_color = '';
    $the_query = new WP_Query($args);
    if ($the_query->have_posts()) {
      while ($the_query->have_posts()) {
        if ($row_count % 2 == 0) {
          $background_color = 'background:#fff';
        } else {
          $background_color = 'background:#eee';
        }
        $the_query->the_post(); ?>
        <tr style="<?php echo $background_color; ?>" id="entry_<?php the_ID(); ?>">
          <td style="width:75%">
            <?php the_title(); ?>
          </td>
          <td><?php the_time('j M, Y'); ?></td>
          <td id="action_entry_<?php the_ID(); ?>" style="cursor:pointer">Add</td>
        </tr> <?php
        $row_count++;
      }
      wp_reset_query();
    }?>
  </tbody>
</table>
