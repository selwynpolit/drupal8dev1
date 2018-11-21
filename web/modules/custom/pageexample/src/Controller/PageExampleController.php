<?php
/**
 * @file
 * Contains \Drupal\page_example\Controller\PageExampleController.
 */

namespace Drupal\pageexample\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Controller routines for page example routes.
 */
class PageExampleController extends ControllerBase {
  /**
   * Constructs a page with descriptive content.
   *
   * Our router maps this method to the path 'examples/page_example'.
   */
  public function description() {
    $simple_url = Url::fromRoute('pageexample_simple');
    $simple_link = Link::fromTextAndUrl(t('simple page'), $simple_url)->toString();

    $arguments_url = Url::fromRoute('pageexample_description', [], ['absolute' => TRUE]);
    $arguments_link = Link::fromTextAndUrl(t('arguments page'), $arguments_url)->toString();

    $build = array(
      '#markup' => t('<p>The Page example module provides two pages, "simple" and "arguments".</p>' .
        '<p>The @simple_link just returns a renderable array for display.</p>' .
        '<p>The @arguments_link takes two arguments and displays them, as in @arguments_url.</p>',
        array(
          '@simple_link' => $simple_link,
          '@arguments_link' => $arguments_link,
          '@arguments_url' => $arguments_url->toString()
        )
      ),
    );

    return $build;
  }

  /**
   * Constructs a simple page.
   *
   * The router _controller callback, maps the path 'examples/page_example/simple'
   * to this method.
   *
   * _controller callbacks return a renderable array for the content area of the
   * page. The theme system will later render and surround the content with the
   * appropriate blocks, navigation, and styling.
   *
   */
  public function simple() {
    return [ '#markup' => '<p>' . t('Simple page: The quick brown fox jumps over the lazy dog.') . '</p>', ];
  }

}
