<?php

namespace Drupal\amp\Service;

use Drupal\amp\Service\DrupalAMP;
use Lullabot\AMP\Validate\ParsedValidatorRules;
use Lullabot\AMP\Spec\ValidationRulesFactory;

/**
 * {@inheritdoc}
 */
class DrupalParsedValidatorRules extends ParsedValidatorRules {

    /**
     * {@inheritdoc}
     */
    public static function getSingletonParsedValidatorRules(){
      if (!empty(self::$parsed_validator_rules_singleton)) {
        return self::$parsed_validator_rules_singleton;
      }
      else {
        /** @var ValidatorRules $rules */
        $rules = ValidationRulesFactory::createValidationRules();

        // Add our own rules to the hard-coded list in validator-generated.php.
        DrupalAMP::additionalRules($rules);

        self::$parsed_validator_rules_singleton = new self($rules);
        return self::$parsed_validator_rules_singleton;
      }
    }

}
