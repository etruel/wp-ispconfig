=== WP-ISPConfig ===
Contributors: etruel
Donate link: esteban@netmdp.com
Tags:  host, ISPConfig, hosting, remote, manager, admin, panel, control, wordpress, post, plugin, interfase, server
Requires at least: 4.9
Tested up to: 5.3
Stable tag: trunk
License: GPLv2

WordPress interface for ISPConfig ~ Hosting Control Panel.  The plugin allows you to add a new client with all needed steps with just one click.

== Description == 

The [first](https://www.howtoforge.com/community/threads/wordpress-plugin-to-create-a-complete-new-client-in-one-step.63285/) WordPress interface for [ISPConfig](http://www.ispconfig.org) – Hosting Control Panel. An excelent Open Source, transparent, free Server Manager.

As a remote user, with WP-ISPConfig plugin you can manage new account and client setup features of your ISPConfig 3 – Hosting Control Panel.

With WP-ISPConfig you can have a WordPress installed in the same host, or in a remote host, and add what each new client needs all-in-one click. 

This means the Client, DNS, Domain (website), FTP user, email Domain and mailbox, all just with minimal input and no complicated setup. 

All you have to do is just activate and type in a few lines of settings.



If you like it, please take a minute to [Rate 5 Stars](https://wordpress.org/support/view/plugin-reviews/wp-ispconfig?rate=5#postform) on Wordpress. Thanks! :-)

Some very useful Add-ons are on the way. You can sponsor one to speed the releases.  If you need some new feature ask for it on http://etruel.com/support/

Author page in spanish:[NetMdP](https://www.netmdp.com).
Plugin and Add-ons page:[etruel.com](https://etruel.com).

== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

= Using the Plugin Manager =

1. Click Plugins
2. Click Add New
3. Search for `wp-ispconfig`
4. Click Install
5. Click Install Now
6. Click Activate Plugin
7. Now you must see WP-ISPConfig Item on Wordpress menu

= Manually =

1. Upload the whole plugin folder to your /wp-content/plugins/ folder.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. That's all, everything will work automatically


= License =

This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with WP Nofollow More Links. If not, see <http://www.gnu.org/licenses/>.


== Screenshots ==

1. Screen: adding an entire website just with minimal data.
2. Settings page to connect to the remote server with Soap or REST API ISPConfig.
3. The default values to use in the fields when create websites.
4. The Dashboard of the plugin shows a summary of the websites in the hosting.

== Frequently Asked Questions ==

= Are this a hosting application ? =
* No. It is far away of do that.  This is just for manage remote user of third-party application for hosting: ISPConfig.  I'm using it and is very nice and free ;-)

= Are my password stored in database ? =
* Yes. You must put remote user and password of ISPConfig and save before insert a Client. (You can delete the field after use if you want)

= Just one feature ? =
* For now... just one and very useful, but a lot are comming soon.  May be also pro features.

= If I need another default data for new client ? =
* You can edit the code very easy for set default data or request new feature at http://etruel.com/contact-us/. 

= Can I ask for another question, support or new features ? =
* Come to website of the plugin [etruel.com](http://etruel.com/contact-us/) and ask there.  I will contact you. Promise.

== Changelog ==
= 3.1 Nov 2, 2019 =
* Added PHP type and version selects to New Website form.
* Added SSL and Let's Encrypt SSL certificate options to New Website form.
* Added Demo Mode to New Website to see what would be create.
* Added FTP and DB User to New Website for existing clients.
* DNS zones are not added if xfer=0 is not found in DNS template content.
* Fixes user mail and maildir on created domains for existing clients.
* Fixes do not create email domain if Email checkbox is not selected.

= 3.0.1 Sep 27, 2018 =
* Added a "loading" notice on ajax options in the select of client websites on domain alias screen.

= 3.0 Sep 20, 2018 =
* Major version released! Test it before use in a production environment.
* Up to version number 3 to be equal with the ISPConfig major version. ;-)
* NEW: Added options to add a new full website to existent clients.
* NEW: Added options to choose what must be created with the website creation: FTP, DB User, DNS, Email.
* NEW: Added options to add domain alias for existent websites of clients.
* Added a remote conection tester with just a click.
* Added optional use of the SOAP or the new improved REST API of ISPConfig.
* Added Server select when necessary.
* Added a Dashboard Page of the plugin (needs improvements but it's a good start ;)
* Added a section in a Settings Tab to fill in the Default Values you use in most of the cases.
* Added custom Wordpress filters for all the parameters sent by the ISPConfig API.
* Improves security nonces in all around.
* Improves the use of admin post hook to send all POST data.
* Improves all Settings with the WordPress standard methods: register_setting, add_settings_section, add_settings_field, etc.

= 1.0.3 =
* Fixes a bug that breaks settings page in some cases.

= 1.0.2 =
* Tested with Wordpress 4.4 and ISPConfig 3.0.5.4 Patch 8
* Added some filters and actions, planning the future...
* Default settings for new domains changed to fit some improvements on ISPConfig API.
* Fixes some notices of PHP strict standards.
* Added images and a fresh "better english" description. Thanks Terence Milbourn, was many time ago but here it is. ;) 

= 1.0.1 =
* Default settings for new domains changed.  Auto subdomain => 'www', php => 'fast-cgi'

= 1.0 =
* Initial submission on WordPress repository

== Upgrade Notice ==

= 3.1 =
* Fixes some important issues and add new demo mode! Test it before use in a production environment.