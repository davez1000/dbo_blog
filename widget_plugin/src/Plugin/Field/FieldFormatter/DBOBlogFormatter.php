<?php

namespace Drupal\dbo_blog\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\dbo_middleware_connect\Utilities\UtilityClass;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Field\FieldDefinitionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Logger\LoggerChannelTrait;

/**
 * Plugin implementation of the 'dbo_blog_tag_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "dbo_blog_tag_formatter",
 *   label = @Translation("DBO blog tag formatter"),
 *   field_types = {
 *     "field_dbo_blog_tag"
 *   }
 * )
 */
class DBOBlogFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * Middleware utilities.
   *
   * @var \Drupal\dbo_middleware_connect\Utilities\UtilityClass
   */
  protected $util;

  /**
   * DBOBlogFormatter constructor.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\dbo_middleware_connect\Utilities\UtilityClass $utilities
   *   Middleware utility class.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    UtilityClass $utilities
  ) {
    parent::__construct($plugin_id,
      $plugin_definition,
      $field_definition,
      $settings,
      $label,
      $view_mode,
      $third_party_settings
    );
    $this->util = $utilities;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        // Implement default settings.
      ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('dbo_middleware_connect.utilities')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
        // Implement settings form.
      ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $field_name = $item->getFieldDefinition()->getName();
      $elements[$delta] = $this->renderMarkup($item, $field_name);
    }

    return $elements;
  }

  /**
   * Renders the markup for the widgety bits.
   *
   * @param $item
   *  Each element.
   *
   * @return string
   *  Rendered markup.
   */
  public function renderMarkup($item, $field_name) {
    $output = '';

    switch ($field_name) {
      case 'field_blog_widget':
        $blog_search_tag_override = (!empty($item->value)) ? $item->value : '';
        break;
      case 'field_blog_heading':
        $heading = $item->value;
        break;
      case 'field_blog_link':

        break;
    }

    // Automatically determine the blog search tag based on URL.
    $request_path = \Drupal::request()->getRequestUri();

    // Use the tag entered in the field to override the URL one.
    $blog_search_tag = !empty($blog_search_tag_override) ? $blog_search_tag_override : substr($request_path, strrpos($request_path, '/') + 1);

    $blog_tag_title = str_replace('-', ' ', $blog_search_tag);
    $blog_tag_title = ucwords($blog_tag_title);

    $blog_base_url = "http://www.zqwx.com/test-life";
    if (strlen($blog_search_tag) > 0) {
      if ($this->util->isFirstSection()) {
        $blog_base_url = "http://www.zqwx.com/test-life";
      }
      else {
        if ($this->util->isNSW()) {
          $blog_base_url = "http://www.vvvv.com/stuff-tales";
        }
      }
    }

    $blog_content = file_get_contents($blog_base_url . '/tag/' . $blog_search_tag . '/feed/');
//    if (empty($heading)) {
//      if ($this->util->isFirstSection()) {
//        $heading = "See the latest from our FirstSection Life blog";
//      }
//      else {
//        if ($this->util->isNSW()) {
//          $heading = "See the latest from our NSW Tales blog";
//        }
//      }
//    }

    if (!empty($blog_content)) {
      try {
        $xml = simplexml_load_string($blog_content);

        if (
          !empty($xml->channel->item) &&
          count($xml->channel->item) > 0
        ) {
          $blogs_display = [];

          foreach ($xml->channel->item as $key => $post) {
            // Only use this blog post if it has a featured URL.
            if (empty($post->featured_image_url)) {
              continue;
            }
            $blogs_display[] = $post;
            if (count($blogs_display) == 3) {
              break;
            }
          }

          $t_blog_tag = t("More @tag_title blogs", ['@tag_title' => $blog_tag_title]);

          if (count($blogs_display) == 3) {
            // Push it out to template for cleaner living.
            $output = [
              '#theme' => 'dbo_blog',
              '#blogs_display' => $blogs_display,
              '#link' => (string) $xml->channel->link,
              '#heading' => $heading,
              '#blog_base_url' => $blog_base_url,
              '#blog_search_tag' => $blog_search_tag,
              '#t_blog_tag' => $t_blog_tag,
            ];

//            print_r($output);
//            exit;

            return $output;

          }
        }
      } catch (\Exception $e) {
        $this->getLogger('dbo_blog_formatter')->error($e->getMessage());
      }
    }
  }

}
