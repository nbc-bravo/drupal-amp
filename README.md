# Accelerated Mobile Pages (AMP)

## Introduction

## Requirements

* [AMP Theme](https://www.drupal.org/project/amptheme)
* [AMP PHP Library](https://github.com/Lullabot/amp-library)


## Introduction

The AMP module is designed to convert Drupal pages into pages that comply with the AMP standard. Initially only node pages will be converted. Other kinds of pages will be enabled at a later time.

When the AMP module is installed, AMP can be enabled for any node type. At that point, AMP content becomes available on URLs such as `node/1?amp` or `node/article-title?amp`. There are also special AMP formatters for text, image, and video fields.

The [AMP Theme](https://www.drupal.org/project/amptheme) is designed to produce the very specific markup that the AMP HTML standard requires. The AMP theme is triggered for any node delivered on an `?amp` path. As with any Drupal theme, the AMP theme can be extended using a subtheme, allowing publishers as much flexibility as they need to customize how AMP pages are displayed. This also makes it possible to do things like place AMP ad blocks on the AMP page using Drupal's block system.

The [AMP PHP Library](https://github.com/Lullabot/amp-library) analyzes HTML entered by users into rich text fields and reports issues that might make the HTML non-compliant with the AMP standard.  The library does its best to make corrections to the HTML, where possible, and automatically converts images and iframes into their AMP HTML equivalents. More automatic conversions will be available in the future. The PHP Library is CMS agnostic, designed so that it can be used by both the Drupal 8 and Drupal 7 versions of the Drupal module, as well as by non-Drupal PHP projects.

We have done our best to make this solution as turnkey as possible, but the module, in its current state, is not feature complete. At this point only node pages can be converted to AMP HTML. The initial module supports AMP HTML tags such as `amp-ad`, `amp-pixel`, `amp-img`, `amp-video`, `amp-analytics`, and `amp-iframe`, but we plan to add support for more of the extended components in the near future. For now the module supports Google Analytics, AdSense, and DoubleClick
for Publisher ad networks, but additional network support is forthcoming.


## Supported AMP Components

- [amp-ad](https://www.ampproject.org/docs/reference/amp-ad.html)
- [amp-pixel](https://www.ampproject.org/docs/reference/amp-pixel.html)
- [amp-img](https://www.ampproject.org/docs/reference/amp-img.html)
- [amp-video](https://www.ampproject.org/docs/reference/amp-video.html)
- [amp-analytics](https://www.ampproject.org/docs/reference/extended/amp-analytics.html)
- [amp-iframe](https://www.ampproject.org/docs/reference/extended/amp-iframe.html)

Support for additional [extended components](https://www.ampproject.org/docs/reference/extended.html) is forthcoming.


## Module Architecture Overview

The module will be responsible for the basic functionality of providing an AMP version of Drupal pages. It will:

- Create an AMP view mode, so users can identify which fields in which order should be displayed on the AMP version of a page.
- Create an AMP route, which will display the AMP view mode on an AMP path (i.e. `node/1?amp`).
- Create AMP formatters for common fields, like text, image, video, and iframe that can be used in the AMP view mode to display AMP-compatible markup for those fields.
- Create AMP ad blocks that can be placed by the theme.
- The theme can place AMP pixel items in the page markup where appropriate, based on the configuration options.
- Create an AMP configuration page where users can identify which ad and analytics systems to use, and identify which theme is the AMP theme.
- Create a way for users to identify which content types should provide AMP pages, and a way to override individual nodes to prevent them from being displayed as AMP pages (to use for odd pages that wouldn’t transform correctly).
- Make sure that paths that should not work as AMP pages generate 404s instead of broken pages.
- Make sure that aliased paths work correctly, so if `node/1` has an alias of `my-page`, `node/1?amp` has an alias of `my-page?amp`.
- Create a system so the user can preview the AMP page.

The body field presents a special problem, since it is likely to contain lots of invalid markup, especially embedded images, videos, tweets, and iframes. There is no easy way to convert a blob of text with invalid markup into AMP-compatible markup. At the same time, this is a common problem that other projects will run into. Our solution is to create a separate, stand-alone, [AMP PHP Library](https://github.com/Lullabot/amp-library) to transform that markup, as best it can, from non-compliant HTML to AMP HTML. The AMP formatter for the body will use that library to render the body in the AMP view mode.


## Installation with Drush
* Download the theme, module, and composer manager: `drush dl amp, amptheme, composer_manager`
* Enable Composer Manager and the AMP Theme: `drush en composer_manager, amptheme, ampsubtheme_example`
* Composer Manager writes a file to `sites/default/files/composer`
* Enable AMP: `drush en amp`
* As long as Composer Manager is enabled, the required dependencies will be added to `sites/all/vendor` as soon as you enable the AMP module
* If you don't see any dependencies download, try `drush composer-json-rebuild` followed by `drush composer-manager install` when in docroot
* Check `/admin/config/system/composer-manager` to ensure it's all green
* Tip: If you ever want to update your composer dependencies to a more recent version (while respecting versioning constraints) try `drush composer-manager update`
* Tip: See composer manager drupal documentation to understand how this all works


## Configuration
* Go to the AMP configuration screen at `/admin/config/content/amp`

### Content Types
* Find the list of your content types at the top
* Click the link to "Enable AMP in Custom Display Settings"
* Open "Custom Display Settings" fieldset, check AMP, click Save button (this brings you back to the AMP config form)
* Click "Configure AMP view mode"
* Set your Body field to use the `AMP text` format (and any other fields you want to configure)
* Click Save button (this brings you back to the AMP config form)

### Analytics (optional)
* Enter your Google Analytics Web Property ID and click save
* This will automatically be added to the AMP version of your pages

### Adsense (optional)
* Enter your Google AdSense Publisher ID and click save
* Visit `/admin/structure/block` to configure add Adsense blocks to your layout (currently up to 4)

### DoubleClick (optional)
* Enter your Google DoubleClick for Publishers Network ID and click save
* Visit `/admin/structure/block` to configure add DoubleClick blocks to your layout (currently up to 4)

### AMP Pixel (optional)
* Check the "Enable amp-pixel" checkbox
* Fill out the domain name and query path boxes
* Click save


## Current maintainers:

- Matthew Tift - https://www.drupal.org/u/mtift
- Marc Drummond - https://www.drupal.org/u/mdrummond
- Sidharth Kshatriya - https://www.drupal.org/u/sidharth_k
