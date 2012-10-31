<p>
  <input type="checkbox" name="show_tax_path" id="app_show_tax_path" value="yes" <?php Checked($this->get_option('show_tax_path'), 'yes') ?> />
  <label for="app_show_tax_path"><?php Echo $this->t('Show the full path for hierarchical taxonomies.') ?></label>
</p>

<p>
  <input type="checkbox" name="show_only_on_singulars" id="app_show_only_on_singulars" value="yes" <?php checked($this->get_option('show_only_on_singulars'), 'yes') ?>/>
  <label for="app_show_only_on_singulars"><?php Echo $this->t('Display the associated posts only on single view.') ?></label>
</p>

<p>
  <input type="checkbox" name="show_page_navigation" id="show_page_navigation" value="yes" <?php checked($this->get_option('show_page_navigation'), 'yes') ?>/>
  <label for="show_page_navigation"><?php Echo $this->t('Display page navigation below the posts content. (Only required if your theme does not support it.)') ?></label>
</p>
