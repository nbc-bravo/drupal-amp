<?php

namespace Drupal\amp\Cache;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\CacheContextInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Defines the AMP amp cache context service.
 *
 * Cache context ID: 'url.amp'.
 *
 * This allows for caching based on whether 'amp' is present as a query
 * parameter. URL.query_args only allows testing the value of a parameter,
 * not whether or not the parameter is present, which we want to check.
 *
 * @deprecated will be removed at some point after
 *   https://www.drupal.org/node/2729439 lands in Drupal core.
 */
class AmpUrlAmpCacheContext extends ContainerAware implements CacheContextInterface {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new BookNavigationCacheContext service.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   *
   * @deprecated will be removed at some point after
   *   https://www.drupal.org/node/2729439 lands in Drupal core.
   */
  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   *
   * @deprecated will be removed at some point after
   *   https://www.drupal.org/node/2729439 lands in Drupal core.
   */
  public static function getLabel() {
    return t("AMP");
  }

  /**
   * {@inheritdoc}
   *
   * @deprecated will be removed at some point after
   *   https://www.drupal.org/node/2729439 lands in Drupal core.
   */
  public function getContext() {
    return (bool) $this->requestStack->getCurrentRequest()->query->has('amp');
  }

  /**
   * {@inheritdoc}
   *
   * @deprecated will be removed at some point after
   *   https://www.drupal.org/node/2729439 lands in Drupal core.
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
