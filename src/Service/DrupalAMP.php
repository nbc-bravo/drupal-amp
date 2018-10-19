<?php

namespace Drupal\amp\Service;

use Lullabot\AMP\AMP;
use Lullabot\AMP\Spec\ValidatorRules;
use Lullabot\AMP\Spec\TagSpec;
use Lullabot\AMP\Spec\AttrSpec;
use Lullabot\AMP\Spec\ValidationRulesFactory;
use Drupal\amp\Service\DrupalParsedValidatorRules;

/**
 * {@inheritdoc}
 */
class DrupalAMP extends AMP {

  /**
   * {@inheritdoc}
   */
  public function __construct() {

    // The DrupalParsedValidator will merge our additional rules into the
    // rules array, giving us more control over what is removed or not.
    $this->parsed_rules = DrupalParsedValidatorRules::getSingletonParsedValidatorRules();
    $this->rules = $this->parsed_rules->rules;
  }

  /**
   * Additional rules to those hard-coded in validator-generated.php
   */
  public static function additionalRules($rules) {

    // Drupal placeholders are valid elements that should not be stripped out.
    $att = new AttrSpec();
    $att->name = 'token';
    $tag = new TagSpec();
    $tag->tag_name = 'head-placeholder';
    $tag->spec_name = 'head-placeholder';
    $tag->spec_url = 'https://www.drupal.org';
    $tag->mandatory = false;
    $tag->unique = true;
    $tag->attrs[] = $att;
    $rules->tags[] = $tag;

    $att = new AttrSpec();
    $att->name = 'token';
    $tag = new TagSpec();
    $tag->tag_name = 'css-placeholder';
    $tag->spec_name = 'css-placeholder';
    $tag->spec_url = 'https://www.drupal.org';
    $tag->mandatory = false;
    $tag->unique = true;
    $tag->attrs[] = $att;
    $rules->tags[] = $tag;

    $att = new AttrSpec();
    $att->name = 'token';
    $tag = new TagSpec();
    $tag->tag_name = 'js-placeholder';
    $tag->spec_name = 'js-placeholder';
    $tag->spec_url = 'https://www.drupal.org';
    $tag->mandatory = false;
    $tag->unique = true;
    $tag->attrs[] = $att;
    $rules->tags[] = $tag;

    return $rules;
  }

}
