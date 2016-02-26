<?php

/**
 * @file
 * Contains \Drupal\amp\Render\AmpHtmlResponseMarkupProcessor.
 */

namespace Drupal\amp\Render;

use Drupal\Core\Render\HtmlResponse;
use Drupal\amp\Service\AMPService;
use Lullabot\AMP\Validate\Scope;

/**
 * Processes markup of HTML responses.
 *
 */
class AmpHtmlResponseMarkupProcessor {

  /**
   * The original content.
   *
   * @var string
   */
  protected $content;

  /**
   * The AMP-processed content.
   *
   * @var string
   */
  protected $ampContent;


  /**
   * The AMP library service.
   *
   * @var AMPService
   */
  protected $ampLibraryService;

  /**
   * The AMP library converter.
   *
   * @var \Lullabot\AMP\AMP
   */
  protected $ampConverter;

  /**
   * Constructs an AmpHtmlResponseMarkupProcessor object.
   *
   * @param AMPService $amp_library_service
   *   An amp library service.
   */
  public function __construct(AMPService $amp_library_service) {
    $this->ampService = $amp_library_service;
    $this->ampConverter = $this->ampService->createAMPConverter();
  }

  /**
   * Processes the content of a response into amp html.
   *
   * @param \Drupal\Core\Render\HtmlResponse $response
   *   The response to process.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The processed response, with the content updated to amp markup.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the $response parameter is not the type of response object
   *   the processor expects.
   */
  public function processMarkupToAmp(HtmlResponse $response) {

    if (!$response instanceof HtmlResponse) {
      throw new \InvalidArgumentException('\Drupal\Core\Render\HtmlResponse instance expected.');
    }

    // Get a reference to the content.
    $this->content = $response->getContent();

    // Disable this feature for now. We also dont want to run the converter.
    //$this->ampConverter->loadHtml($this->content, ['scope' => Scope::HTML_SCOPE]);
    //$this->ampContent = $this->ampConverter->convertToAmpHtml();

    // @todo this is for debugging only. We need to have a better way to check for validation issues though.
    // $this->ampContent .= "<--! FULL DOCUMENT VALIDATION\n " . $this->ampConverter->warningsHumanText() . "-->";

    // Return the processed content.
    // $response->setContent($this->ampContent);

    return $response;
  }
}
