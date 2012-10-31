<script type="text/javascript">
var $delete_confirm_message = "<?php Echo $this->t('Are you sure you want to delete this template?') ?>";
</script>

<ol>
  <?php ForEach ( $this->find_templates() AS $file => $properties ) : ?>
  <li>
    <input type="radio" name="default_template_file" id="template_<?php Echo Sanitize_Title($file) ?>" value="<?php Echo HTMLSpecialChars($file) ?>" <?php Checked($this->get_default_template(), $file) ?>/>
    <label for="template_<?php Echo Sanitize_Title($file) ?>">
    <?php If (Empty($properties['name'])) : ?>
      <em><?php Echo $file ?></em>
    <?php Else : ?>
      <strong><?php Echo $properties['name'] ?></strong>
    <?php EndIf; ?>
    <?php If ($properties['version']) : ?> <small>(<?php Echo $properties['version'] ?>)</small><?php Endif; ?>
    <?php If ($properties['author'] && !$properties['author_uri'] ) : ?>
      <?php Echo $this->t('by') ?> <?php Echo $properties['author'] ?>
    <?php ElseIf ($properties['author'] && $properties['author_uri'] ) : ?>
      <?php Echo $this->t('by') ?> <a href="<?php Echo $properties['author_uri'] ?>" target="_blank"><?php Echo $properties['author'] ?></a>
    <?php Endif; ?>
    (<small><a href="<?php Echo $this->Option_Page_Url(Array('delete' => $file)) ?>" title="<?php Echo $this->t('Delete this template') ?>" class="delete-link"><?php Echo $this->t('Delete') ?></a></small>)
    <?php If ($properties['description']) : ?><br /><?php Echo $properties['description']; Endif; ?><br />
    <small><?php PrintF($this->t('Found in <em>%s</em>.'), $file) ?></small>
    </label>
  </li>
  <?php EndForEach; ?>
</ol>

<p>(<small><?php Echo $this->t('You can tick a template to make it the default one.') ?></small>)</p>
