<h4><?php Echo $this->t('Install a template in ZIP format') ?></h4>
<p><?php Echo $this->t('If you have a template in a .zip format, you may install it by uploading it here.') ?></p>
<p><?php Echo $this->t('Template as ZIP File') ?>: <input type="file" name="template_zip" /></p>

<h4><?php Echo $this->t('Install a template in PHP/CSS format') ?></h4>
<p><?php Echo $this->t('If you have a template in a .php (and .css format), you may install it by uploading it here.') ?></p>
<p><?php Echo $this->t('Templates PHP File') ?>: <input type="file" name="template_php" /></p>
<p><?php Echo $this->t('Templates CSS File') ?> <small>(<?php Echo $this->t('not required') ?>)</small>: <input type="file" name="template_css" /></p>

<p><input type="submit" value="<?php Echo $this->t('Install template and save all options') ?>" class="button-primary" /></p>
