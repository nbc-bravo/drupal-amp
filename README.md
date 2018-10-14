# Accelerated Mobile Pages (AMP)

## Requirements

* [AMP PHP Library](https://github.com/Lullabot/amp-library)

## Suggestions
* [AMP Theme](https://www.drupal.org/project/amptheme) - Provides themes you can use or sub class for AMP themes. Not required if you have a custom theme that doesn't require it, which you know is AMP-compliant.
* [Schema.org Metadata](https://www.drupal.org/project/schema_metatag) - Not required for the AMP module be be functional, but will be necessary to create valid AMP markup.
* If you have the core Toolbar module enabled, also enable the AMP Toolbar module.
* If you have the core RDF module enabled, also enable the AMP RDF module.


## Introduction

The AMP module is designed to convert Drupal pages into pages that comply with the AMP standard. At this time, only node pages are converted.

When the AMP module is installed, AMP can be enabled for any node type. At that point, AMP content becomes available on URLs such as `node/1?amp=1` or `node/article-title?amp=1`. There are also special formatters for text, image, and video fields geared towards outputting the appropriate AMP components.

## Install AMP for Drupal

You should be [using Composer to manage Drupal site dependencies](https://www.drupal.org/docs/develop/using-composer/using-composer-to-manage-drupal-site-dependencies).

With Composer, adding AMP tools to your site project is much like adding other modules or themes with Composer. On the command line, enter the following commands in your project root directory:

`composer require drupal/amp --with-dependencies`

If you want to create a custom AMP theme, add that theme to the /themes directory.

## Enable AMP

* Enable the AMP module. 
* Install at least one AMP theme, which can be the ExAMPle subtheme included in amptheme or a custom AMP theme you create. 
  * To install an AMP subtheme through the user interface, go to `/admin/appearance`.
  * Install the AMP theme, but do not make it the default theme. AMP themes will automatically be used only on AMP pages.
  * Go to the theme settings page, `/admin/appearance/settings/{AMP-SUBTHEME-NAME}`. Uncheck the box to use the theme's default logo and upload a logo for the AMP subtheme, then save that change. An AMP logo should be no bigger than 60px high and 600px wide, so you may need a different logo than your primary theme.

### Provide initial AMP configuration
Go to `/admin/structure/amp` and select your AMP configuration options:

#### Theme
* Select a theme for the AMP pages. The subtheme you installed in the previous step should appear as an option and that is the one you should select.
* Select and save the options.

#### Content Types
* Find the list of your content types at the top of the AMP Configuration page.
* Click the link to "Enable AMP in Custom Display Settings".
* Open "Custom Display Settings" fieldset, check AMP, click Save button (this brings you back to the AMP config form).
* Click "Configure AMP view mode".
* The AMP view mode is where you can control which fields will display on the AMP page for each content type. You might only want a title, image, and body.
* There are special formatters for text, image, and iframe fields in order to output AMP components, so be sure to use them in the AMP view mode. Make sure to use the AMP Text formatter for the body field.
* Click Save button (this brings you back to the AMP config form).
* To change these later, go to `/admin/structure/types/manage/{CONTENT-TYPE}/display/amp` and set up the fields for the AMP version of each content type.

### Set up blocks for AMP pages
Go to `/admin/structure/block/list/{AMP-SUBTHEME-NAME}` and set up the blocks for the AMP page.

The AMP page can be a simple page, with a header, content area, and footer. You can remove most blocks from this theme, for instance just displaying the branding, title and content on the page. Start simple and add more elements later if desired.

## Configure Structured Data for AMP

AMP pages require Schema.org metadata be provided as JSON-LD in the head of the page. This can be accomplished using the [Schema.org Metadata module](https://www.drupal.org/project/schema_metadata). See links to documentation and other instructions on its project page. 

That module contains numerous sub-modules that can be used to display Schema.org metadata on various kinds of content. At a minimum, enable the Schema.org Metatag base module and the Schema.org Article module. Then configure your article content type to display article metadata.

* Compare your JSON with the [Article guidelines](https://developers.google.com/search/docs/data-types/articles).
* If there is no public page available for Google to read, you can just copy the script markup into the [Structured Data Testing tool](https://search.google.com/structured-data/testing-tool) to verify that all information meets the requirements.

## Module Architecture Overview

###[AMP Theme](https://www.drupal.org/project/amptheme)

The theme is designed to produce the very specific markup that the AMP standard requires. The AMP theme is triggered for any node delivered on an `?amp=1` path. As with any Drupal theme, the AMP theme can be extended using a subtheme, allowing publishers as much flexibility as they need to customize how AMP pages are displayed. You will likely want to create your own custom AMP subtheme with your own styles.

A new feature in AMP 8.3 is the ability to create an AMP theme as a subtheme of your primary theme instead of using the base AMPTheme. An example is included in AMPTheme of an AMP theme that is a subtheme of Bartik instead of AMPTheme. Your subtheme should carefully follow the examples in that theme to be sure it still validates.

### [AMP PHP Library](https://github.com/Lullabot/amp-library)

The library analyzes HTML entered by users into rich text fields and reports issues that might make the HTML non-compliant with the AMP standard.  The library does its best to make corrections to the HTML, where possible, and automatically converts images and iframes into their AMP equivalents. The PHP Library is CMS agnostic, designed so that it can be used by both the Drupal 8 and Drupal 7 versions of the Drupal module, as well as by non-Drupal PHP projects. The Composer installation will take care of adding this library when the AMP module is added, as long as you append the command with `--with-dependencies`.

### [AMP Module](https://www.drupal.org/project/amp)
The module is responsible for the basic functionality of providing an AMP version of Drupal pages, including the following tasks:

- Provides an AMP view mode, so users can decide which fields should be displayed in which order on the AMP version of a page.
- Provides an AMP route, which will display the AMP view mode on an AMP path (i.e. `node/1?amp=1`).
- Provides formatters for common fields, like text, image, video, and iframe that can be used in the AMP view mode to display AMP components for those fields.
- Provides an AMP configuration page where users can identify which theme is the AMP theme.
- Provides an AMP view mode, a way for users to identify which content types should provide AMP pages, and override individual nodes to prevent them from being displayed as AMP pages (to use for odd pages that wouldn’t transform correctly).
- Makes sure that paths that should not work as AMP pages generate 404s instead of broken pages.
- Makes sure that aliased paths work correctly, so if `node/1` has an alias of `my-page`, `node/1?amp=1` has an alias of `my-page?amp=1`.
- Creates a system so the user can preview the AMP page.

The body field presents a special problem, since it is likely to contain lots of invalid markup, especially embedded images, videos, tweets, and iframes. There is no easy way to convert a blob of text with invalid markup into AMP markup. At the same time, this is a common problem that other projects will run into. Our solution is a separate, stand-alone, [AMP PHP Library](https://github.com/Lullabot/amp-library) to transform that markup, as best it can, from non-compliant HTML to AMP. The AMP Text field formatter for the body will use that library to render the body in the AMP view mode.

## Supported AMP components

We have done our best to make this solution as turnkey as possible, but more could be added to this module in the future. At this point only node pages can be converted to AMP. The module currently supports AMP tags such as `amp-ad`, `amp-pixel`, `amp-img`, `amp-video`, `amp-analytics`, and `amp-iframe`. 


- [amp-ad](https://www.ampproject.org/docs/reference/amp-ad.html)
- [amp-pixel](https://www.ampproject.org/docs/reference/amp-pixel.html)
- [amp-img](https://www.ampproject.org/docs/reference/amp-img.html)
- [amp-video](https://www.ampproject.org/docs/reference/amp-video.html)
- [amp-analytics](https://www.ampproject.org/docs/reference/extended/amp-analytics.html)
- [amp-iframe](https://www.ampproject.org/docs/reference/extended/amp-iframe.html)

Support for additional [extended components](https://www.ampproject.org/docs/reference/extended.html) is gradually being added.

## How to disable AMP
If you choose to disable AMP on your site for one or all content types, you can do so through the AMP configuration page at `/admin/config/content/amp`.

* View the list of AMP-enabled content types.
* For each AMP-enabled content type you wish to disable, click the link to "Disable AMP in Custom Display Settings".
* Open the "Custom Display Settings" fieldset, uncheck AMP, and click the Save button (this brings you back to the AMP config form).

Once the AMP view mode is disabled, that content type will no longer display AMP-formatted pages when `?amp=1` is appended to a URL for that content.

