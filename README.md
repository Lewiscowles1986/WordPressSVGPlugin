![CI](https://github.com/Lewiscowles1986/WordPressSVGPlugin/workflows/CI/badge.svg)
![Release](https://github.com/Lewiscowles1986/WordPressSVGPlugin/workflows/Release/badge.svg)
[![Latest Version](https://img.shields.io/github/release/Lewiscowles1986/WordPressSVGPlugin.svg)](https://github.com/Lewiscowles1986/WordPressSVGPlugin/releases)
[![GitHub License](https://img.shields.io/badge/license-GPLv3-yellow.svg)](https://raw.githubusercontent.com/Lewiscowles1986/WordPressSVGPlugin/master/LICENSE)

# Enable SVG Uploads WordPress Plugin

This plugin enables SVG uploads in WordPress Media Library with very little overhead.

## Installation

Download and extract the zip file or clone this repo to your WordPress plugins directory.

After an upgrade to 2.x it may be necessary to download and re-upload your SVG media files so that the grid works. This is because we now use simple XML to retrieve the width & height of the SVG.

### Requirements

* WordPress 4.4 or higher (advised to use latest)
* PHP 7.1 or higher (EOL versions may work, but you should still run this on latest stable)
  * PHP 7.3 is the minimum to run the tests due to phpunit 9

### Updates

Automatic updates are supported via [GitHub Updater](https://github.com/afragen/github-updater).

## Feedback

Please feel free to suggest improvements, report conflicts, and/or make suggestions for hooks to integrate with third-party plugins.

## Goals

- Install / start container / VM for each release since
- Download from GitHub & install plugin
- Use browser automation to test [(details)](https://github.com/Lewiscowles1986/WordPressSVGPlugin/issues/5)
