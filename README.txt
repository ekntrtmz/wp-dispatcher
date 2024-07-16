=== WP Dispatcher ===
Contributors: ekndev,joanrodas
Donate link: ekndev
Tags: secure downloads, temporary downloads, download links
Requires at least: 3.0.1
Tested up to: 6.5
Stable tag: 1.3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Generate secure download links dynamically from your uploads using shortcodes.

== Description ==

With the plugin WP DISPATCHER you can create an temporary and secure download link for your file uploads. Your uploads are secured in a special folder. The secure download links are created by shortcodes which you can use anywhere. Every shortcode call generates a new download link so be cautious where to use it (best suited within automatic emails).

- Create temporary download links for any file 
- Automatically expire download links after a time limit limit 
- Know how many times the file has been downloaded.

First upload your files to a secured folder. Then generate expiring links to your protected uploads. Usage with wp shortcodes. 

Caution: Files are protected via .htaccess. This works only in LAMP environment with Apache as Webserver.

== Installation ==


1. Upload `wp-dispatcher.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Developer Note ==
Create a Pull Request against a new branch on Github: https://github.com/ekntrtmz/wp-dispatcher

== SVN Update Note ==
Copy newest version into trunk and commit changes to trunk.
Copy trunk to a new tag with semver. Push changes to new tag.
Examples:  https://developer.wordpress.org/plugins/wordpress-org/how-to-use-subversion/#examples


== Changelog ==

= 1.3 =
* Minor improvements:
    - Add optional parameter "exp" to set expiration hours in shortcode
    - Add translatable strings
    - Update "Custom_WP_List_Table"
    - Add order in columns

* Minor fixes:
    - Prevent link generation in admin

= 1.2.1 =
* Fix critical issue

= 1.2 =
* Minor Fix:
    - fix db prefixing

= 1.1 =
* Minor improvements:
    - set expiration time limit in plugin settings
    - fix sanitizing & validation issues

= 1.0 =
* This version contains very basic features: 
    - upload files to secure folder
    - generate links from shortcodes

