=== Enable SVG Uploads ===
Contributors: LewisCowles,hendridm
Tags: svg,upload,media library,mime
Requires at least: 4.0
Tested up to: 5.5.1
Requires PHP: 7.0
Stable tag: 2.1.1
License: GPL-3.0
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Enable SVG uploads in Media Library and other file upload fields.

== Description ==
This plugin enables SVG uploads in WordPress Media Library with very little overhead.

#### Feedback

Please feel free to [suggest](https://github.com/Lewiscowles1986/WordPressSVGPlugin/issues) improvements, report conflicts, and/or make suggestions for hooks to integrate with third-party plugins.

#### Updates

Automatic updates are currently supported via [GitHub Updater](https://github.com/afragen/github-updater).

== Installation ==
Download and extract the zip file or clone this repo to your WordPress plugins directory.

After an upgrade to 2.x it may be necessary to download and re-upload your SVG media files so that the grid works. This is because we now use simple XML to retrieve the width & height of the SVG.

== Changelog ==
= 2.1.1 =
* Update tested up to

= 2.1.0 =
* GitHub -> WordPress Plugin Directory
* GitHub CI
* PHPUnit update
* Contribution issue format
* Confirmed working for 5.5.1

= 2.0.2 =
* Modified CSS added (we now get CSS size from correctly marked up SVG's anyway)

= 2.0.0 =
* Added filter to deduce dimensions (if present) from regular SVG uploads

= 1.9.1 =
* Added filter to deduce dimensions (if present) from regular SVG uploads

= 1.8.4 =
* Fixed a typo leading to warnings on some hosts

= 1.8.3 =
* GitHub force push error remit

= 1.8.2 =
* Pulling in Extension Logic from composer dependency

= 1.8.1 =
* Version Release & Enhancements from hendridm

= 1.8.0 =
* Altered Directory Structure for GitHub plugin updater compatibility

= 1.7.0 =
* Fixed issue with oversized emojis
* Replaced URLs with WP-generated alternatives

= 1.6.4 =
* Fixed compatibility issue with WordPress 4.7.2

= 1.6.3 =
* Minor release adding support for WordPress 4.7
