<?php

namespace Drupal\amp\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\amp\Routing\AmpContext;

/**
 * Redirects AMP requests to ?_wrapper_format=amp if appropriate.
 */
class AmpEventSubscriber implements EventSubscriberInterface {

  /**
   * AMP context service.
   *
   * @var Drupal\amp\Routing\AmpContext
   */
  protected $ampContext;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * Constructs an AmpEventSubscriber object.
   *
   * @param Drupal\amp\Routing\AmpContext $ampContext
   *   The AMP context service.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The RouteMatch service.
   */
  public function __construct(AmpContext $ampContext, RouteMatchInterface $routeMatch) {
    $this->ampContext = $ampContext;
    $this->routeMatch = $routeMatch;
  }

  /**
   * Alters the wrapper format if this is an AMP request.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent $event
   *   The event to process.
   */
  public function onView(GetResponseForControllerResultEvent $event) {

    // See if this is a request that already uses the wrapper.
    $amp_wrapper_format = isset($_GET['_wrapper_format']) && $_GET['_wrapper_format'] == 'amp';

    // See if this route and object are AMP, without checking the active theme.
    $isAmpRoute = $this->ampContext->isAmpRoute($this->routeMatch, NULL, FALSE);

    // Get the current request.
    $request = $event->getRequest();

    // Redirect requests that are not AMP routes to standard html processing.
    if (!$isAmpRoute) {
      $request->query->set(MainContentViewSubscriber::WRAPPER_FORMAT, 'html');
    }
    // Redirect ?amp requests to ?_wrapper_format=amp
    elseif (!$amp_wrapper_format && $isAmpRoute) {
      $request->query->set(MainContentViewSubscriber::WRAPPER_FORMAT, 'amp');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Run before main_content_view_subscriber.
    $events[KernelEvents::VIEW][] = ['onView', 1];
    return $events;
  }

}
