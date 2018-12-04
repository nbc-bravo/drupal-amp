<?php

namespace Drupal\amp\Plugin\Field\FieldFormatter;

use Drupal\amp\Plugin\Field\FieldFormatter\AmpImageFormatter;
use Drupal\Core\Field\EntityReferenceFieldItemListInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem;

/**
 * Plugin for amp media image formatter.
 *
 * @FieldFormatter(
 *   id = "amp_media",
 *   label = @Translation("Amp Media Image"),
 *   description = @Translation("Display media as an AMP Image file."),
 *   field_types = {
 *     "entity_reference",
 *   }
 * )
 *
 * @see \Drupal\amp\Plugin\Field\FieldFormatter\AmpImageFormatter
 * @see \Drupal\media\Plugin\Field\FieldFormatter\MediaThumbnailFormatter
 */
class AmpMediaImageFormatter extends AmpImageFormatter {

  /**
   * {@inheritdoc}
   *
   * This has to be overriden because FileFormatterBase expects $item to be
   * of type \Drupal\file\Plugin\Field\FieldType\FileItem and calls
   * isDisplayed() which is not in FieldItemInterface.
   */
  protected function needsEntityLoad(EntityReferenceItem $item) {
    return !$item->hasNewEntity();
  }

  /**
   * {@inheritdoc}
   */
  protected function getEntitiesToView(EntityReferenceFieldItemListInterface $items, $langcode) {
    $media = parent::getEntitiesToView($items, $langcode);
    $entities = [];
    foreach ($media as $media_item) {
      $entity = $media_item->thumbnail->entity;
      $entity->_referringItem = $media_item->thumbnail;
      $entities[] = $entity;
    }
    return $entities;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getFieldStorageDefinition()->getSetting('target_type') == 'media';
  }

}
