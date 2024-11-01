=== Plugin Name ===
Contributors: boonebgorges
Tags: wpmu, blogroll, links, widget
Requires at least: WPMU 2.8
Tested up to: WPMU 2.9.1.1
Stable tag: trunk

Creates a widget that pulls a category of links from any other blog on the same WPMU installation

== Description ==

This plugin adds a widget to your WPMU blog that allows the blog administrator to display a list of links from any blog on the installation. Imagine, for instance, that Josie is teaching a class in which she and each of her students have individual blogs. Josie's got a list of important links (a class blogroll, or maybe links that are relevant to the class), and she'd like to share the links with her students. Using this plugin, her students can place a widget on their blogs that automatically pulls in a certain category of links from her blog.

At the moment, the plugin works like this: Drop the Shared Blogroll widget into one of your blog's sidebars. You'll need to know the blog_id of the blog from which you'll be pulling the links. (This is the big weakness in the plugin for now. For the moment, I'll probably add a line of code to the Dashboard that echoes the global $blog_id, so that blog owners will know their blog_id and be able to pass it along to other who want to share their links. In the future I may add autocomplete or something. If you have a suggestion, let me know.) Once you've entered the source blog id, the plugin automatically populates the Category dropdown list with the categories from the source blog, so if you only want to include one of the link categories you can. You can have as many shared blogroll widgets on a blog as you'd like.

== Installation ==

You have two installation options:
1. Upload the directory '/shared-blogroll/' to your WP plugins directory, and activate either sitewide (recommended) or on individual blogs
1. Upload ONLY the file 'shared-blogroll.php' to your mu-plugins directory, for automatic loading on each blog


== Changelog ==

= 0.1 =
* Initial release

= 1.0 =
* Additional AJAX support
