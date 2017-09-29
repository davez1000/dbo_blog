<?php

namespace Drupal\dbo_blog\Alter;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\dbo_middleware_connect\Utilities\UtilityClass;

/**
 * Class DboBlogNodeViewAlter.
 *
 * @package Drupal\dbo_blog\Alter
 */
class DboBlogNodeViewAlter extends EntityViewDisplay {

  use StringTranslationTrait;

  /**
   * Entity type manager interface.
   *
   * @var EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity wrapper interface.
   *
   * @var EntityWrapperInterface
   */
  protected $entityWrapper;

  /**
   * Current user.
   *
   * @var AccountInterface
   */
  protected $currentUser;

  /**
   * Middleware utilities.
   *
   * @var UtilityClass
   */
  protected $util;

  /**
   * DboBlogEntityViewAlter constructor.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   Entity type manager.
   * @param AccountInterface $currentUser
   *   Current logged in user.
   * @param UtilityClass $utility_class
   *   Middleware utilities.
   */
  public function __construct(
    EntityTypeManagerInterface $entityTypeManager,
    AccountInterface $currentUser,
    UtilityClass $utility_class
  ) {
    $this->certifierEntity = $entityTypeManager;
    $this->currentUser = $currentUser;
    $this->util = $utility_class;
  }

  /**
   * Node view alter.
   *
   * @param array $build
   *   Build array.
   * @param EntityInterface $entity
   *   Entity interface.
   * @param EntityViewDisplayInterface $display
   *   Display interface.
   */
  public function nodeViewAlter(
    array &$build,
    EntityInterface $node,
    EntityViewDisplayInterface $display
  ) {

    $node_type = $node->getType();
    switch ($node_type) {
      case 'widget_blog';
        $tag = $node->field_blog_widget_tag->value;
        $heading = $node->field_blog_heading->value;
        $link_url = $node->field_blog_link->uri;
        $link_text = $node->field_blog_link->title;

        // Use the tag entered in the field to override the URL one.
        $request_path = \Drupal::request()->getRequestUri();
        $blog_search_tag = !empty($tag) ? $tag : substr($request_path, strrpos($request_path, '/') + 1);

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
        if (!empty($blog_content)) {
          try {

            $xml = simplexml_load_string($blog_content);

            if (!empty($xml->channel->item) && count($xml->channel->item) > 0) {
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
                $build = [
                  '#theme' => 'dbo_blog',
                  '#blogs_display' => $blogs_display,
                  '#link' => $link_url,
                  '#heading' => $heading,
                  '#blog_base_url' => $blog_base_url,
                  '#blog_search_tag' => $blog_search_tag,
                  '#t_blog_tag' => $t_blog_tag,
                ];

              }
            }
          } catch (\Exception $e) {
            $this->getLogger('dbo_blog_formatter')->error($e->getMessage());
          }
        }
        break;
    }
  }

}
