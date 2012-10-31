<p>
  <input type="checkbox" name="disable_update_notification" id="app_disable_update_notification" value="yes" <?php checked($this->get_option('disable_update_notification'), 'yes') ?>/>
  <label for="app_disable_update_notification"><?php Echo $this->t('Disable Update notification.') ?></label>
</p>

<h4><?php Echo $this->t('Your DennisHoppe.de Account') ?></h4>
<p>
  <label for="app_update_username"><?php _e('Username') ?>:</label>
  <input type="text" name="update_username" id="app_update_username" value="<?php Echo $this->get_option('update_username') ?>">
</p>

<p>
  <label for="app_update_password"><?php _e('Password') ?>:</label>
  <input type="password" name="update_password" id="app_update_password" value="<?php Echo $this->get_option('update_password') ?>">
</p>
