<?php

namespace Drupal\config_ignore_language;

use Drupal\Core\Config\StorageComparer as CoreStorageComparer;
use Drupal\Core\Config\StorageInterface;

/**
 * Overridden StorageComparer so we can control certain aspects of configuration
 * comparison. Unfortunately this is not a service so we cannot replace it
 * entirely.
 */
class StorageComparer extends CoreStorageComparer {

  /**
   * {@inheritdoc}
   */
  public function getAllCollectionNames($include_default = TRUE) {
    $collections = parent::getAllCollectionNames($include_default);
    $ignored_collections_settings = [
      'language.*',
    ];

    $collections = array_filter($collections, function ($collection) use ($ignored_collections_settings) {
      $collection_fill = array_fill(0, count($ignored_collections_settings), $collection);
      $filter_results = array_map('fnmatch', $ignored_collections_settings, $collection_fill);
      return !in_array(TRUE, $filter_results);
    });

    // If somehow the default collection got unset and we're told to include it,
    // this will include it.
    if ($include_default) {
      array_unshift($collections, StorageInterface::DEFAULT_COLLECTION);
    }

    return array_unique($collections);
  }

}

