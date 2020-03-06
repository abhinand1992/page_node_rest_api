<?php

namespace Drupal\page_node_rest_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides node API for the content type page.
 *
 * @RestResource(
 *   id = "page_node",
 *   label = @Translation("Page Node"),
 *   uri_paths = {
 *     "canonical" = "/page_json/{api_key}/{node_id}"
 *   }
 * )
 */
class PageNode extends ResourceBase {

  /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get($api_key, $nid) {

    $node = \Drupal::entityTypeManager()->getStorage('node')->load($nid);

    if ($node) {

      // Site API Key configuration value.
      $api_key_saved = \Drupal::config('page_node_rest_api.config')->get('siteapikey');

      // Make sure the nodeid and API key is valid.
      if ($api_key_saved != 'No API Key yet' && $api_key_saved == $api_key && $node->getType() == 'page' && $node->isPublished()) {

        // Respond with the json representation of the node.
        return new ResourceResponse(['status' => 'success', 'data' => ['title' => $node->get('title')->value, 'body' => $node->get('body')->value]], 200, ['Content-Type'=> 'application/json']);
      }
    }

    // Respond with access denied.
    return new ResourceResponse(array('status' => 'failed', 'error' => 'access denied'), 401, ['Content-Type' => 'application/json']);
  }

}
