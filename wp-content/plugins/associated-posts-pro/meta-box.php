<?php

// Read Meta data for this post
$meta = $this->get_association_data();

// Print selectable taxonomies
ForEach ((Array) $this->get_option('taxonomy_selection') AS $post_type => $arr_taxonomy){
  // Get post type
  If (!$post_type = Get_Post_Type_Object($post_type)) Continue;
    
  ForEach ((Array) $arr_taxonomy AS $taxonomy => $_){
    // Handle the taxonomy
    If ($taxonomy == '_wp_user') : ?>
      <h4 class="toggle-title">
        <?php PrintF ($this->t('Select %s by Author'), $post_type->label) ?>
        <span class="hidden active">(<?php Echo $this->t('Active') ?>)</span>
      </h4>
      <div class="toggle-box hide-if-js">    
        <p class="select-mode">
          <?php Echo $this->t('Selection mode:') ?>
          <select name="<?php Echo $this->Field_Name('post_selection') ?>[<?php Echo $post_type->name ?>][<?php Echo $taxonomy ?>][mode]">
            <option value="add" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy]['mode'], 'add') ?> ><?php PrintF($this->t('Add to my selection: All %s created by these Authors.'), $post_type->label) ?></option>
            <option value="filter" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy]['mode'], 'filter') ?> ><?php PrintF($this->t('Filter my selection: Only %s created by these Authors.'), $post_type->label) ?></option>
            <option value="" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy]['mode'], '') ?> ><?php Echo $this->t('Do not care about the Authors.') ?></option>
          </select>
        </p>
        <p class="select-author">
        <?php ForEach ( (Array) $this->get_authors() AS $author ) : ?>
        <span class="option-short">
          <input type="checkbox" name="<?php Echo $this->Field_Name('post_selection') ?>[<?php Echo $post_type->name ?>][<?php Echo $taxonomy ?>][selection][]" id="<?php Echo $post_type->name ?>_<?php Echo $taxonomy ?>_<?php Echo $author->ID ?>" value="<?php Echo $author->ID ?>" <?php Checked(In_Array($author->ID, (Array) @$meta['post_selection'][$post_type->name][$taxonomy]['selection'])) ?> />
          <label for="<?php Echo $post_type->name ?>_<?php Echo $taxonomy ?>_<?php Echo $author->ID ?>"><?php Echo $author->display_name ?></label>
        </span>
        <?php EndForEach; ?>
        </p>
      </div>

    <?php ElseIf ( $taxonomy == '_explicitly' ) : ?>
      <h4 class="toggle-title">
        <?php PrintF($this->t('Select %s explicitly (Additionally to your selection)'), $post_type->label) ?>
        <span class="hidden active">(<?php Echo $this->t('Active') ?>)</span>
      </h4>
      <div class="toggle-box hide-if-js">
        <p class="select-post">
        <?php ForEach ((Array) $this->get_all_posts($post_type->name, $GLOBALS['post']->ID) AS $p) : ?>
        <span class="option-long">
          <input type="checkbox" name="<?php Echo $this->Field_Name('post_selection') ?>[<?php Echo $post_type->name ?>][<?php Echo $taxonomy ?>][selection][]" id="<?php Echo $post_type->name ?>_<?php Echo $taxonomy ?>_<?php Echo $p->ID ?>" value="<?php Echo $p->ID ?>" <?php Checked(In_Array($p->ID, (Array) @$meta['post_selection'][$post_type->name][$taxonomy]['selection'])) ?> />
          <label for="<?php Echo $post_type->name ?>_<?php Echo $taxonomy ?>_<?php Echo $p->ID ?>"><?php Echo ($p->post_title != '') ? $p->post_title : '<i>'.SPrintF($this->t('Post %s (Without title)'), $p->ID).'</i>' ?></label>
        </span>
        <?php EndForEach; ?>
        </p>
      </div>
    
    <?php ElseIf ($taxonomy = Get_Taxonomy($taxonomy)) : ?>
      <h4 class="toggle-title">
        <?php PrintF($this->t('Select %1$s by %2$s'), $post_type->label, $taxonomy->label) ?>
        <span class="hidden active">(<?php Echo $this->t('Active') ?>)</span>
      </h4>
      <div class="toggle-box hide-if-js">
        <p class="select-mode">
          <?php Echo $this->t('Selection mode:') ?>
          <select name="<?php Echo $this->Field_Name('post_selection') ?>[<?php Echo $post_type->name ?>][<?php Echo $taxonomy->name ?>][mode]">
          <?php If ($taxonomy->hierarchical) : ?>
            <option value="add_or" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'add_or') ?> ><?php PrintF ($this->t('Add to my selection: All %1$s which are at least in one of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="filter_or" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'filter_or') ?> ><?php PrintF ($this->t('Filter my selection: Only %1$s which are at least in one of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="add_and" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'add_and') ?> ><?php PrintF ($this->t('Add to my selection: All %1$s which are in all of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="filter_and" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'filter_and') ?> ><?php PrintF ($this->t('Filter my selection: Only %1$s which are in all of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], '') ?> ><?php PrintF($this->t('Do not care about %s.'), $taxonomy->label) ?></option>
          <?php Else: ?>
            <option value="add_or" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'add_or') ?> ><?php PrintF ($this->t('Add to my selection: All %1$s with at least one of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="filter_or" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'filter_or') ?> ><?php PrintF ($this->t('Filter my selection: Only %1$s with at least one of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="add_and" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'add_and') ?> ><?php PrintF ($this->t('Add to my selection: All %1$s with all of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="filter_and" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], 'filter_and') ?> ><?php PrintF ($this->t('Filter my selection: Only %1$s with all of these %2$s.'), $post_type->label, $taxonomy->label) ?></option>
            <option value="" <?php Selected($meta['post_selection'][$post_type->name][$taxonomy->name]['mode'], '') ?> ><?php PrintF ($this->t('Do not care about %s.'), $taxonomy->label) ?></option>      
          <?php EndIf; ?>
          </select>
        </p>
        <p class="select-terms">
        <?php ForEach ( get_terms($taxonomy->name, Array('hide_empty' => False)) AS $term ) : ?>
          <?php If ($this->get_option('show_tax_path') && $taxonomy->hierarchical) : ?> 
            <span class="option-long">
          <?php Else : ?>
            <span class="option-short">
          <?php EndIf; ?>
          <input type="checkbox" name="<?php Echo $this->Field_Name('post_selection') ?>[<?php Echo $post_type->name ?>][<?php Echo $taxonomy->name ?>][selection][]" id="<?php Echo $post_type->name ?>_<?php Echo $taxonomy->name ?>_<?php Echo $term->term_id ?>" value="<?php Echo $term->term_id ?>" <?php Checked(In_Array($term->term_id, (Array) @$meta['post_selection'][$post_type->name][$taxonomy->name]['selection'])) ?> />
          
          <label for="<?php Echo $post_type->name ?>_<?php Echo $taxonomy->name ?>_<?php Echo $term->term_id ?>">
          <?php If ($this->get_option('show_tax_path') && $taxonomy->hierarchical) : ?>
            <?php Echo $this->get_term_path($term); ?>
          <?php Else: ?>
            <?php Echo $term->name; ?>
          <?php EndIf; ?>
          </label>
        </span>
        <?php EndForEach; ?>
        </p>
      </div>
    <?php EndIf;
    
  }
}
?>

<h4><?php _e('Settings') ?></h4>

<p class="offset">
  <label for="app_offset"><?php Echo $this->t('Offset:') ?></label> <input type="text" name="<?php Echo $this->Field_name('offset') ?>" id="app_offset" value="<?php Echo HTMLSpecialChars($meta['offset']) ?>" size="4" /> (<?php Echo $this->t('Leave blank to start with the first post.') ?>)<br />
  <small><?php Echo $this->t('With the offset you can pass over posts which would normally be collected by your selection.') ?></small>
</p>

<p class="posts-per-page">
  <label for="app_posts_per_page"><?php Echo $this->t('Posts per page:') ?></label> <input type="text" name="<?php Echo $this->Field_name('posts_per_page') ?>" id="app_posts_per_page" value="<?php Echo HTMLSpecialChars($meta['posts_per_page']) ?>" size="4" /> (<?php Echo $this->t('Leave blank to show all posts on one page.') ?>)
</p>

<p class="disable-pagination">
  <?php Echo $this->t('Disable pagination:') ?> <input type="checkbox" name="<?php Echo $this->Field_name('disable_pagination') ?>" id="app_disable_pagination" value="yes" <?php Checked($meta['disable_pagination'], 'yes') ?> />
  <label for="app_disable_pagination"><?php Echo $this->t('Do not display the pagination for this post.') ?></label>
</p>

<p class="order-by">
  <label for="app_order_by"><?php Echo $this->t('Order posts by:') ?></label>
  <select name="<?php Echo $this->Field_Name('order_by') ?>" id="app_order_by">
    <option value="date" <?php Selected($meta['order_by'], 'date') ?> ><?php _e('Date') ?></option>
    <option value="author" <?php Selected($meta['order_by'], 'author') ?> ><?php _e('Author') ?></option>
    <option value="title" <?php Selected($meta['order_by'], 'title') ?> ><?php _e('Title') ?></option>
    <option value="modified" <?php Selected($meta['order_by'], 'modified') ?> ><?php _e('Last Modified') ?></option>
    <option value="menu_order" <?php Selected($meta['order_by'], 'menu_order') ?> ><?php _e('Post Order (Order field in the Edit Page Attributes box)') ?></option>
    <option value="rand" <?php Selected($meta['order_by'], 'rand') ?> ><?php _e('Random order') ?></option>
    <option value="comment_count" <?php Selected($meta['order_by'], 'comment_count') ?> ><?php _e('Number of Comments') ?></option>
    <option value="id" <?php Selected($meta['order_by'], 'id') ?> ><?php _e('Post ID') ?></option>
    <option value="meta_value" <?php Selected($meta['order_by'], 'meta_value') ?> ><?php Echo $this->t('Meta Value') ?></option>
    <option value="meta_value_num" <?php Selected($meta['order_by'], 'meta_value_num') ?> ><?php Echo $this->t('Meta Value (Numeric)') ?></option>
  </select>
</p>

<p class="meta-key hide-if-js">
  <label for="app_meta_key"><?php Echo $this->t('Meta key name:') ?></label>
  <input type="text" name="<?php Echo $this->Field_Name('meta_key') ?>" id="app_meta_key" value="<?php Echo HTMLSpecialChars($meta['meta_key']) ?>" /><br />
  <small><?php Echo $this->t('Please notice: This will only work if <strong>all</strong> posts in your selection will have this meta key!') ?></small>
</p>

<p class="order">
  <label for="app_order"><?php Echo $this->t('Order:') ?></label>
  <select name="<?php Echo $this->Field_Name('order') ?>" id="app_order">
    <option value="DESC" <?php Selected($meta['order'], 'DESC') ?> ><?php Echo $this->t('Descending') ?></option>
    <option value="ASC" <?php Selected($meta['order'], 'ASC') ?> ><?php Echo $this->t('Ascending') ?></option>
  </select>
</p>


<h4><?php Echo $this->t('Template') ?></h4>
<div class="template">
  <?php ForEach ( $this->find_templates() AS $file => $properties ) : ?>
  <p>
    <input type="radio" name="<?php Echo $this->Field_Name('template') ?>" id="template_<?php Echo Sanitize_Title($file) ?>" value="<?php Echo HTMLSpecialChars($file) ?>" <?php Checked($meta['template'], $file) ?> <?php Checked(!$meta['template'] && $file == $this->get_default_template()) ?> />
    <label for="template_<?php Echo Sanitize_Title($file) ?>">
    <?php If (Empty($properties['name'])) : ?>
      <em><?php Echo $file ?></em>
    <?php Else : ?>
      <strong><?php Echo $properties['name'] ?></strong>
    <?php EndIf; ?>
    <?php If ($properties['version']) : ?> (<?php Echo $properties['version'] ?>)<?php Endif; ?>
    <?php If ($properties['author'] && !$properties['author_uri'] ) : ?>
      <?php Echo $this->t('by') ?> <?php Echo $properties['author'] ?>
    <?php ElseIf ($properties['author'] && $properties['author_uri'] ) : ?>
      <?php Echo $this->t('by') ?> <a href="<?php Echo $properties['author_uri'] ?>" target="_blank"><?php Echo $properties['author'] ?></a>
    <?php Endif; ?>
    <?php If ($properties['description']) : ?><br /><?php Echo $properties['description']; Endif; ?>
    </label>
  </p>
  <?php EndForEach; ?>
</div>
