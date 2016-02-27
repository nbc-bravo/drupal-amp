# Accelerated Mobile Pages (AMP)

## Introduction

The AMP module is designed to convert Drupal pages into pages that comply with
the AMP standard . Initially only node pages will be converted. Other kinds of
pages will be enabled at a later time.

When the AMP module is installed, AMP can be enabled for any node type. At that
point, a new AMP view mode is created for that content type, and AMP content
becomes available on URLs such as node/1/amp or node/article-title/amp. We also
created special AMP formatters for text, image, and video fields.

The AMP theme is designed to produce the very specific markup that the AMP HTML
standard requires. The AMP theme is triggered for any node delivered on an /amp
path. As with any Drupal theme, the AMP theme can be extended using a subtheme,
allowing publishers as much flexibility as they need to customize how AMP pages
are displayed. This also makes it possible to do things like place AMP ad
blocks on the AMP page using Drupal's block system.

The PHP Library analyzes HTML entered by users into rich text fields and
reports issues that might make the HTML non-compliant with the AMP standard.
The library does its best to make corrections to the HTML, where possible, and
automatically converts images and iframes into their AMP HTML equivalents. More
automatic conversions will be available in the future. The PHP Library is CMS
agnostic, designed so that it can be used by both the Drupal 8 and Drupal 7
versions of the Drupal module, as well as by non-Drupal PHP projects.

We have done our best to make this solution as turnkey as possible, but the
module, in its current state, is not feature complete. At this point only node
pages can be converted to AMP HTML. The initial module supports AMP HTML tags
such as amp-ad, amp-pixel, amp-img, amp-video, amp-analytics, and amp-iframe,
but we plan to add support for more of the extended components in the near
future. For now the module supports Google Analytics, AdSense, and DoubleClick
for Publisher ad networks, but additional network support is forthcoming.


## Requirements

* [AMP theme](https://www.drupal.org/project/amptheme)
* [AMP PHP Library](https://github.com/Lullabot/amp-library).


## Current maintainers:

- Matthew Tift - http://www.matthewtift.com
- Marc Drummond - https://www.drupal.org/u/mdrummond
- Sidharth Kshatriya - https://www.drupal.org/u/sidharth_k
