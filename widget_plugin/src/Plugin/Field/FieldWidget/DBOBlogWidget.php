<?php

namespace Drupal\dbo_blog\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'dbo_blog_widget' widget.
 *
 * @FieldWidget(
 *   id = "dbo_blog_tag_widget",
 *   label = @Translation("DBO blog tag widget"),
 *   field_types = {
 *     "field_dbo_blog_tag"
 *   }
 * )
 */
class DBOBlogWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => 30,
      'placeholder' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $value = isset($items[$delta]->value) ? $items[$delta]->value : '';
    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => $value,
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => $this->getFieldSetting('max_length'),
    ];

    return $element;
  }

}
