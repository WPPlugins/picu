=== picu ===
Plugin Name: picu
Plugin URI: https://picu.io/
Contributors: florianziegler, pandulu, haptiq
Tags: photography, collection, image, images, picture, pictures, proof, proofing, photographer, client, clients, client proofing, photos, photo album, album, albums, thumbnails, media, gallery, galleries, lightbox
Requires at least: 4.0
Tested up to: 4.7
Stable tag: 1.1.1
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Send a collection of photographs to your client for approval.

== Description ==

**Client Proofing for Photographers.**

Say goodbye to overcomplicated communication with your clients:
Shoot. Upload. Get Approved.

All from the comfort of your own WordPress installation.

- With picu you can create collections of photographs from your photo-shoots.
- Send your client a link to a collection via email as part of the workflow.
- Once the client approved the collection, you will be informed via email.
- Easily retrieve your client's selection to use in your photo administration software of choice.


**Enhance picu with premium Add-Ons**

We give our best to provide support and answer questions in the support forums, but please be aware that one-on-one priority support via email is only guaranteed to people who bought one of our [premium Add-Ons](https://picu.io/add-ons/).

- Check out the [Brand & Customize](https://picu.io/add-ons/brand-and-customize/) add-on, which lets you add a custom logo and adapt the frontend to match your branding.
- The [Import](https://picu.io/add-ons/import/) add-on gives you the ability to upload large amounts of images via FTP and import them directly from your web server.
- The [Selection Options](https://picu.io/add-ons/selection-options) add-on allows you set the number of images your client needs to select to approve a collection.


= Requirements =
* PHP 5.6
* WordPress 4.5

= Website =
* [picu.io](https://picu.io/)

= Authors =
* [Claudio Rimann](http://claudiorimann.com/)
* [Florian Ziegler](http://florianziegler.de/)


== Installation ==

1. Upload the `picu` folder to your `/wp-content/plugins` directory.
2. Activate the "picu" plugin in the WordPress administration interface.


== Screenshots ==

1. picu in the WordPress admin: Simply create a collection of photos and send it to your client.
2. How a collection looks to your client.
3. View approved images in a grid or as a list. Conveniently copy filenames of approved images.


== Changelog ==

= 1.1.1 =
Release Date: May 26th, 2017

* Enhancements
	* Copy filenames into the clipboard with one click!
	* Add blog URL and active theme to our debug page
	* Final preparations for Selection Options add-on release

* Bugfixes
	* Remove a bug that prevented some collections from being duplicated

= 1.1.0 =
Release Date: April 14th, 2017

* Enhancements
	* Add possibility to filter selected/unselected images in the front end
	* Add a new column to the collection overview screen, that shows how many images have been selected
	* New default image size is now 3000px wide (was 1024px before)
	* Small design refinements
	* Add filter for picu collection slug
	* JS templates will now work with asp style php tags enabled
	* Attachment pages for picu images will not show up in Yoast xml sitemaps, attachment pages will redirect to the homepage
	* Preparation for upcoming Selection Options add-on release

= 1.0.0 =
Release Date: December 9th, 2016

* Enhancements
	* Revised notification system with more meaningful notices when saving and sending collections
	* More preparation for upcoming add-on releases
	* Speaking of add-ons: Check out https://picu.io/add-ons for the first two add-ons!

= 0.9.5 =
Release Date: October 11th, 2016
* Enhancements
	* Preparation for upcoming add-on releases

* Bugfixes
	* Removes an embarrassing bug which added "test: " before email subjects

= 0.9.4 =
Release Date: August 16th, 2016

* Enhancements
	* You can now duplicate collections
	* Update how picu sends emails
	* Prevent picu collections form showing up in Yoast SEO's xml sitemaps
	* More preparation for upcoming add-on releases

= 0.9.3 =
Release Date: June 7th, 2016

* Enhancements
	* Add rudimentary print css
	* Add page with debug information under settings
	* picu can now be translated by everyone via https://translate.wordpress.org/projects/wp-plugins/picu
	* Update Add-Ons page
	* Preparation for upcoming add-on releases

= 0.9.2 =
Release Date: April 21st, 2016

* Enhancements
	* Add filter to change the default filename separator, see picu FAQs for details
	* Add Finnish translation (Thx Rami & Tom)

* Bugfixes
	* Make picu compatible with (some) caching plugins

= 0.9.1 =
Release Date: March 12th, 2016

* Enhancements
	* Redirect clients to the homepage after they submitted their selection
	* Implemented a hook to change the default email message
	* Small styling enhancements

= 0.9.0 =
Release Date: January 22nd, 2016

* Enhancements
	* picu now (officially) supports password protection for collections
	* Added a dialog box when trying to edit a collection that has already been sent to the client
	* Added a loading indicator when saving/sending collections in the front end
	* Lots of small styling enhancements

= 0.8.2 =
Release Date: December 29th, 2015

* Enhancements
	* Added new labels for our Custom Post Type "Collections" (WordPress 4.4+)
	* Added a new filter for customizations through Add-Ons

* Bugfixes
	* Fixed a conflict on some admin screens when $current_screen wasn't set

= 0.8.1 =
Release Date: December 9th, 2015

* Enhancements
	* Added support for native responsive images (WordPress 4.4+)
	* Updated German translations

* Bugfixes
	* Fixed a display error in the backend for WP 4.4+

= 0.8.0 =
Release Date: November 25th, 2015

* Enhancements
	* Completely redesigned backend UI!
	* Added the possibility for other sharing options (instead of email)
	* Re-Organized some files behind the scenes

* Bugfixes
	* Fixed size of thumbnails in backend after upload
	* Fixed a bug on the welcome screen
	* Fixed width of Add-On boxes for large screens

= 0.7.5 =
Release Date: October 29th, 2015

* Enhancements
	* Update translations
	* Added licensing functionality for picu add-ons

* Bugfixes
	* Fixed a bug where a collection would not be displayed correctly
	* Fixed a bug where email content was not formated correctly

= 0.7.4 =
Release Date: October 23rd, 2015

* Enhancements
	* Display maximum file size limit for image uploads
	* HTML emails are now sent by default (can be turned off in Settings)
	* Added some missing translations
	* Added custom hooks and more small preparations for picu add-ons

* Bugfixes
	* Add versioning to js/css files to make sure the right files are loaded, regardless of caching
	* Fixed some display bugs in IE

= 0.7.3 =
Release Date: October 7th, 2015

* Enhancements
	* HTML email notifications
	* Improved compatibility with other plugins
	* Added more under-the-hood goodness to prepare for future updates and the release of picu add-ons

= 0.7.2 =
Release Date: September 12th, 2015

* Enhancements
	* Added a new list view and filter to check approved images in the backend
	* Added preview thumbnails to theme settings
	* Added filter hooks to backbone templates and collections, models & views

* Bugfixes
	* Remove double admin page titles

= 0.7.1 =
Release Date: September 7th, 2015

* Enhancements
	* Added the photographers email address as CC in emails to the client

* Bugfixes
	* Fixed confirmation emails to the photographer when a collection is approved
	* Fixed a bug where images were displayed in the wrong order
	* Fixed a bug where admin notices were displayed in the wrong places
	* Fixed password protection for collections
	* Fixed uninstall routine for multisite installations

= 0.7.0 =
Release Date: September 1th, 2015

* First official release (public beta) to the WordPress.org repo.
