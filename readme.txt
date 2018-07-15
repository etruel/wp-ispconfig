=== WP-ISPConfig ===
Contributors: etruel
Donate link: esteban@netmdp.com
Tags:  host, ISPConfig, hosting, remote, manager, admin, panel, control, wordpress, post, plugin, interfase, server
Requires at least: 3.1
Tested up to: 4.4
Stable tag: trunk
License: GPLv2

WordPress interface for ISPConfig ~ Hosting Control Panel.  The plugin allows you to add a new client with all needed steps with just one click.

== Description == 

The WordPress interface for [ISPConfig](http://www.ispconfig.org) – Hosting Control Panel. An excelent Open Source, transparent, free Server Manager.

As a remote user, with WP-ISPConfig plugin you can manage new account and client setup features of your ISPConfig 3 – Hosting Control Panel.

With WP-ISPConfig you can have WordPress installed on the same host, or in a remote host, and add what every new client needs all-in-one click. 

This means the Client, DNS, Domain (website), FTP user, email Domain and mailbox, all just with minimal input and no complicated setup. 

All you have to do is just activate and type in a few lines of settings.



If you like it, please take a minute to [Rate 5 Stars](https://wordpress.org/support/view/plugin-reviews/wp-ispconfig?rate=5#postform) on Wordpress. Thanks! :-)

Some very useful Add-ons are on the way. You can sponsor one to speed the releases.  If you need some new feature ask for it on http://etruel.com/contact-us/

Author page in spanish:[NetMdP](http://www.netmdp.com). 
Plugin and Add-ons page:[etruel.com](http://etruel.com).

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
2. After add the all-in-one client, the plugin shows you the operations results.

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
= NUEVA VERSION =
* AHORA USA EL STANDAR PARA SETTINGS DE WORDPRESS con register_setting, add_settings_section, add_settings_field
* SE AGREGO UN TESTER DE CONEXION CON UN SOLO CLICK A UN BOTON.
* SE AGREGO NONCE A TODAS LAS GUARDADO DE DATOS.
* SE USA EL ADMIN POST HOOK PARA ENVIAR LOS POSTS DATA.

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

= 1.0.3 =
* Some fixes
