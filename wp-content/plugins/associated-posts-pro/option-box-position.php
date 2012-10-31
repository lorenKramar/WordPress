<p>
  <input type="radio" name="posts_position" id="app_posts_position_bottom" value="bottom" <?php checked($this->get_option('posts_position'), 'bottom') ?>/>
  <label for="app_posts_position_bottom"><?php Echo $this->t('Append the associated posts to the pages content.') ?></label>
</p>

<p>
  <input type="radio" name="posts_position" id="app_posts_position_top" value="top" <?php checked($this->get_option('posts_position'), 'top') ?>/>
  <label for="app_posts_position_top"><?php Echo $this->t('Prepend the associated posts to the pages content.') ?></label>
</p>

<p>
  <input type="radio" name="posts_position" id="app_posts_position_none" value="none" <?php checked($this->get_option('posts_position'), 'none') ?>/>
  <label for="app_posts_position_none"><?php Echo $this->t('<b>Do not</b> show the associated posts automatically. (In this case you have to add the short code manually.)') ?></label>
</p>

<p>
  <label for="app_content_filter_priority"><?php Echo $this->t('Content filter priority') ?>:</label>
  <input type="text" name="content_filter_priority" id="app_content_filter_priority" size="3" value="<?php Echo $this->get_option('content_filter_priority') ?>" /><br />
  (<small><?php Echo $this->t('Appoints the priority of the auto added ShortCode in relation to other plugins.') ?></small>)
</p>
