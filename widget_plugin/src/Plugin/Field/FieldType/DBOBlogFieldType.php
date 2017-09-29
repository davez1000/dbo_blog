<?php

namespace Drupal\dbo_blog\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'dbo_blog_field_type' field type.
 *
 * @FieldType(
 *   id = "field_dbo_blog_tag",
 *   label = @Translation("DBO blog tag"),
 *   description = @Translation("Overriding optional blog tag field"),
 *   default_widget = "dbo_blog_tag_widget",
 *   default_formatter = "dbo_blog_tag_formatter",
 * )
 */
class DBOBlogFieldType extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'max_length' => 255,
      'type' => 'varchar',
      'not_null' => FALSE,
      'size' => 6,
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Tag value'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    $schema = [
      'columns' => [
        'value' => [
          'type' => $field_definition->getSetting('type'),
          'length' => (int) $field_definition->getSetting('max_length'),
          'not null' => $field_definition->getSetting('not_null'),
//          'size' => $field_definition->getSetting('size'),
        ],
      ],
    ];

    return $schema;
  }

}
