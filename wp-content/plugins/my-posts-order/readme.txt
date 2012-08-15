=== My Post Order ===
Contributors: kapilchugh
Tags: custom post order, page order, arrange post order, custom post type, sort post, reorder, featured posts, rss feed, modify order, rearrange posts
Requires at least: 3.0
Tested up to: 3.4
Stable tag: 1.2.1
License: GPLv2 or later

A plugin which allows you to sort posts, pages, custom post type in ANY order and display the same in your sidebar.

== Description ==

While WordPress allows you to make your posts sticky, or even sort them in ascending or descending order, sometimes this is just not enough. What if you want to display the posts in ANY order you need? Unfortunately there is no such functionality in WordPress, which is where this plugin saves the day.

This plugin works on sections of posts, which you can define based on the following three criteria:

1. Select posts individually : You can use the drag-and-drop feature to rearrange posts in ANY order.
2. Select a category : You can also select posts from any category and change the posts order.
3. Show XML/RSS Feed : Here you just need to give the URL of XML Feed and it will fetch latest feed/posts.

You can display ordered posts in theme with query_posts or get_posts or WP_Query like this :
`query_posts('section_name=NAME_OF_SECTION');`

Here NAME_OF_SECTION must be replaced with your actual section name. If no section found with mentioned name then nothing will change.

OR

**Once the sections are created, you can display these posts using widgets.**

* If you are using custom template option in widget then template file should be placed in `custom-templates` folder of your plugin (`/wp-content/plugins/my-posts-order/includes/custom-templates/loop.php`) and (`/wp-content/plugins/my-posts-order/includes/custom-templates/loop_rss.php`).

Now wasn't that easy!

== Installation ==

1. Upload the `my-posts-order` folder to the `/wp-content/plugins/` directory
2. Activate the Plugin through the 'Plugins' menu in WordPress.
3. Go to `My Posts Order Options`.
4. Create new Sections based on different criteria.
5. Go to the Appearance -> Widgets.
6. Drag and Drop `My Posts Order` Widget in the Widget Area.
7. Select the section name.
8. In the same way you can add multiple widgets.
9. If you want to display ordered posts somewhere else in a theme, then use it like this
query_posts('section_name=NAME_OF_SECTION')

== Frequently Asked Questions ==
= How can I use custom template option in widget? =
You need to create loop.php file in your custom-templates folder. For eg.
`<ul>  <?php
    while ($the_query->have_posts()) : $the_query->the_post(); ?>
      <li>
        <span class="mpo_post_title">
          <a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
            <?php if ( get_the_title() ) the_title(); else the_ID(); ?>
          </a>
        </span>
      </li> <?php
    endwhile; ?>
  </ul>`

For for rss option create another file loop_rss.php
`<ul> <?php
    foreach ( $rss_items as $item ) { ?>
      <li>
        <a target="_blank"  href="<?php echo $item->get_permalink();?>"><?php echo $item->get_title(); ?></a>
      </li> <?php
    } ?>
  </ul>`

Please send me your suggestions/feedback/queries to kapil.chugh@hotmail.com and help me to improve this Plugin.

== Screenshots ==
1. Add new section based on `select posts individually` criteria.
2. Add new section based on `category`.
3. Add new section based on `XML/RSS` criteria.
4. Selection of widget.
5. Posts display on site.

== Changelog ==
= 1.2.1 =
* Bug Fixing.

= 1.2 =
* Added an option through which we can display ordered posts anywhere in a theme.
* In widget, we can display posts as a drop down or we can use custom template.
* In Admin panel called Javascript and CSS only on plugin page.
* Increased default posts limit to 300.

= 1.1.1 =
* Fixed a search related issue.

= 1.1 =
* Provision to sort pages, category posts, custom post type.
* UI changes.
* Added prefix in front of functions name.
* Added 'Settings' link while activating plugin.
* Bug Fixing.
* Added previously created sections on home page.

= 1.0.3 =
* Fixed a bug that was causing issue on some servers.

= 1.0.2 =
* Bug Fixing.

= 1.0.1 =
* Removed Notices and Warnings.

= 1.0 =
* Initial release

== Upgrade Notice ==

= 1.0.1 =
This version fixes some bugs . So Upgrade immediately.