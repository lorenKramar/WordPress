=== Plugin Name ===
Contributors: bitinn
Donate link: http://thanks.for.considering.a.donation/you.can.keep.it/
Tags: fast, quick, image, photo, insert, media, admin, edit
Requires at least: 2.6
Tested up to: 3.4
Stable tag: 2.3.1
License: MIT

Fully integrates media manager into editing interface, avoid reloading thickbox pop-up, with enhanced features like multi-insert & mass-editing.

== Description ==

Faster Image Insert aims to do **one thing right**:

Moves built-in Media Manager down in a meta-box, right next to main editing panel, so you have full control of the manager: opens it, makes it collapse or hidden from the interface completely.

Best of all, is now you can insert image(s) much **faster**, and **precisely** where you want them to be.

- **No thickbox**, using metabox with zero interface blocking, quite similar to the uploader in WordPress 1.5
- **No hacking**, default upload interface is not affected, only enhanced.
- **Insert multiple images** in gallery & library mode, without using shortcode; can also insert images in reversed order, and even control spacing between images.
- **Mass info editing**, change title/captions in one-shot.
- **Smart switches**: set default uploader, disable captions.

**This plugin is designed for**:

* Screenshot lover - movie, game or anime review etc.
* Howto guru - cooking guide, hardware DIY guide etc.
* Photo logger - author can comment below each photo.
* Blogger that has been shouting "run, thickbox, RUN!" to the loading screens.

== Installation ==

- Extract to folder /faster-image-insert
- Upload to WordPress plugins directory /wp-content/plugins
- Activate the plugin in WordPress plugin page
- (Optional) Change options in "Settings" menu
- Navigate to post/page editing panel, a Fast Insert metabox should appear

== Screenshots ==

1. Gallery tab
2. Upload tab
3. Settings

== Frequently Asked Questions ==

= Does the media manager behaves the same in meta-box ? =

Yes, except less annoying (I hope).

= Why my items' ordering are not saved ? =

Remember to press "save all changes" button after re-ordering images. "Insert selected images" only outputs in listing order, NOT by ordering numbers.

= Why are blank spaces/lines not saved ? =

WordPress' default editor TinyMCE strips consecutive blank spaces/lines; the action is performed when saving or switching edit mode. This plugin tries to work around it by sending

      <p>&nbsp;</p>


= Conflicts with other plugins ? =

Following plugins are known to either modify the way data is sent to editor, or use a non-default gallery view; with these plugins you may not be able to insert multiple images at once or using our mass editing feature; other FII functions should continue to work.

[Custom Field Template](http://wordpress.org/extend/plugins/custom-field-template/)

[NextGEN Gallery](http://wordpress.org/extend/plugins/nextgen-gallery/)

== Changelog ==

= trunk =
* [unstable release](http://downloads.wordpress.org/plugin/faster-image-insert.zip)

= 2.3.1 =

* Added latest po file for future i18n translation

= 2.3.0 =

* For WordPress 3.4+
* Added image link options to mass edit
* Added translation in German, Dutch, Spanish & Romanian (community provided) 
* Fix an issue where image caption editing does not work
* Fix an issue where checkbox is not displayed when using translation file
* Fix an issue where deprecated user level is used, change to capability 'activate_plugins'
* Clarify known plugin conflicts with other plugins in FAQ

= 2.1.0 =

* Fixed CSRF issue
* Fixed support for HTTPS protocol
* Updated French translation

= 2.0.0 =

* For WordPress 3.0+
* Support custom post types
* Now able to insert multiple images at default upload page
* Remove unnecessary components

= 1.5.1 =
* Provides a new method for reloading the meta-box (click to toggle after autosave);
* New Option for global custom string (inserted between images on multiple insertions);
* New Translation and POT file.

= 1.5.0 =
* Updates for the wordpress 2.8+ user;
* Removes dependence on internal scripts to resolve plugin conflicts;
* Attempts to work around the TinyMCE whitespace stripping problem;
* Compatible mode is now auto-enabled (load quicktags.js if not 2.8);
* Updates FAQ.

= 1.3.7 =
* Resolves a plugin conflict, updates readme.

= 1.3.6 =
* Re-structured setting page layout, new translation, updated compatible version.

= 1.3.5 =
* Add compatibility mode to remove functions that are WP 2.8 built-in, debug mode for plugin conflict debugging, new translation.

= 1.3.4 =
* Ability to reverse image ordering & toggle all image items (remember to save it first). fix a problem regarding mass-edit.

= 1.3.3 =
* Negate selection now available, Danish translation by Georg, French translation by Li-An, users are now able to set separator between images, and disable caption if desired.

= 1.3.2 =
* new experimental mass-image edit (change title, caption, alignment & size; enable it in option page); added i18n support (pot file included), with chinese translation.

= 1.3.1 =
* removed due to wrong file, please use 1.3.2, we apologize for the trouble.

= 1.3.0 =
* provide options for customization & debug (under Settings menu); fix a bug associate with non-image insertion; load image upload form instead of media upload form (they have the identical functions, but latter has better From URL tab support); get ready for i18n; tested support for wordpress 2.6

= 1.2.0 =
* now comes with multiple images insertion feature; various improvements.

= 1.1.1 =
* optimized autosave detection. Final release for WordPress 2.7.1

= 1.1.0 =
* workaround for autosave breaking upload queue, better support for wordpress installation in sub-folder.

= 1.0.3 =
* updated readme, better support for un-saved post.

= 1.0.0 =
* initial release, basic function implemented.

== Upgrade Notice ==

= 2.3.0 =
For WordPress 3.4+ Only

= 2.2.0 =
For WordPress 3.2+ Only

= 2.1.0 =
Includes a security fix to avoid potential CSRF

= 2.0.0 =
For WordPress 3.0+ Only

= 1.5.0 =
For WordPress 2.8+ Only

== Notes ==

Translations:

* Chinese - by [DF](http://bitinn.net/)
* French - by [Li-An](http://www.li-an.fr/)
* Danish - by [Georg](http://wordpress.blogos.dk/)
* Japanese - by [Chibi](http://ilovechibi.net/)
* Russian - by [FatCow](http://www.fatcow.com/)
* Romanian - by [Alexander](http://webhostinggeeks.com/)
* German - by [Jenny](http://www.professionaltranslation.com/)
* Dutch - by [Rene](http://wpwebshop.com)
* Spanish - by [Adrian](http://twitter.com/adrianramiro/)

Plugin License:

* [MIT License](http://en.wikipedia.org/wiki/MIT_License)
