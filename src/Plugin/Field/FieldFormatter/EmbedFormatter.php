<?php

/**
 * @file
 * Contains \Drupal\embed_formatter\Plugin\field\formatter\EmbedFormatter.
 */

namespace Drupal\embed_formatter\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'embed_formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "embed_formatter",
 *   module = "embed_formatter",
 *   label = @Translation("Embed"),
 *   field_types = {
 *     "link",
 *   },
 *   quickedit = {
 *     "editor" = "plain_text"
 *   }
 * )
 */
class EmbedFormatter extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'token_replace' => 0,
      'format' => 'plain_text',
    ) + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element['token_replace'] = array(
      '#type'          => 'checkbox',
      '#description'   => t('Replace text pattern. e.g %node-title-token or %node-author-name-token, by token values.', array(
                            '%node-title-token'       => '[node:title]',
                            '%node-author-name-token' => '[node:author:name]',
                          )) . ' ' /*. $token_link*/,
      '#title'         => t('Token Replace'),
      '#default_value' => $this->getSetting('token_replace'),
    );

    $element['format'] = array(
      '#title'         => t('Format'),
      '#type'          => 'select',
      '#options'       => array(),
      '#default_value' => $this->getSetting('format'),
    );

    $formats = filter_formats();

    foreach ($formats as $formatId => $format) {
      $element['format']['#options'][$formatId] = $format->get('name');
    }

    $element['br'] = array('#markup' => '<br/>');

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
//    $summary = array();
//    $yes     = t('Yes');
//    $no      = t('No');
//
//    $summary[] = t('Token Replace') . ': ' . ($this->getSetting('token_replace') ? $yes : $no);
//
//    $formats = filter_formats();
//    $format  = $this->getSetting('format');
//    $format  = isset($formats[$format]) ? $formats[$format]->name : t('Unknown');
//
//    $summary[] = t('Format: @format', array('@format' => $format));
//
//    $summary = array_filter($summary);
//
//    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $token_data = array(
      'user' => \Drupal::currentUser(),
      $items->getEntity()->getEntityTypeId() => $items->getEntity(),
    );

    foreach ($items as $delta => $item) {
      $output = '<drupal-url data-embed-url="' . $item->uri . '" data-url-provider="Vimeo" />';
      if ($this->getSetting('token_replace')) {
        $output = \Drupal::token()->replace($output, $token_data);
      }

      $output = check_markup($output, $this->getSetting('format'), $item->getLangcode());

      $elements[$delta] = array(
        '#type' => 'processed_text',
        '#text' => $output,
        '#format' => $this->getSetting('format'),
        '#langcode' => $item->getLangcode(),
      );dpm($elements);
    }

    return $elements;
  }
}
