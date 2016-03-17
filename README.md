# Accelerated Mobile Pages (AMP)

## Introduction

**The Drupal 7 version of the AMP module is under active development. Currently, it is NOT functional. The beta version of the Drupal 7 version of the module will be available in mid-March 2016.**

## Installation with Drush
* Download the theme, module, and composer manager: `drush dl amp, amptheme, composer_manager`
* Enable Composer Manager and the AMP Theme: `drush en composer_manager, amptheme, ampsubtheme_example`
* Composer Manager writes a file to `sites/default/files/composer`
* Go to the directory: 'cd sites/default/files/composer`
* Complete installation: `composer install`
* Check `/admin/config/system/composer-manager` to ensure it's all green
* Enable AMP: `drush en amp`

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
