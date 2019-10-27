<?php

namespace Drupal\carousel_block\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\carousel_block\Entity\CarouselEntityInterface;

/**
 * Class CarouselEntityController.
 *
 *  Returns responses for Carousel entity routes.
 */
class CarouselEntityController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Carousel entity  revision.
   *
   * @param int $carousel_entity_revision
   *   The Carousel entity  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($carousel_entity_revision) {
    $carousel_entity = $this->entityManager()->getStorage('carousel_entity')->loadRevision($carousel_entity_revision);
    $view_builder = $this->entityManager()->getViewBuilder('carousel_entity');

    return $view_builder->view($carousel_entity);
  }

  /**
   * Page title callback for a Carousel entity  revision.
   *
   * @param int $carousel_entity_revision
   *   The Carousel entity  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($carousel_entity_revision) {
    $carousel_entity = $this->entityManager()->getStorage('carousel_entity')->loadRevision($carousel_entity_revision);
    return $this->t('Revision of %title from %date', ['%title' => $carousel_entity->label(), '%date' => format_date($carousel_entity->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Carousel entity .
   *
   * @param \Drupal\carousel_block\Entity\CarouselEntityInterface $carousel_entity
   *   A Carousel entity  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(CarouselEntityInterface $carousel_entity) {
    $account = $this->currentUser();
    $langcode = $carousel_entity->language()->getId();
    $langname = $carousel_entity->language()->getName();
    $languages = $carousel_entity->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $carousel_entity_storage = $this->entityManager()->getStorage('carousel_entity');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $carousel_entity->label()]) : $this->t('Revisions for %title', ['%title' => $carousel_entity->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all carousel entity revisions") || $account->hasPermission('administer carousel entity entities')));
    $delete_permission = (($account->hasPermission("delete all carousel entity revisions") || $account->hasPermission('administer carousel entity entities')));

    $rows = [];

    $vids = $carousel_entity_storage->revisionIds($carousel_entity);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\carousel_block\CarouselEntityInterface $revision */
      $revision = $carousel_entity_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $carousel_entity->getRevisionId()) {
          $link = $this->l($date, new Url('entity.carousel_entity.revision', ['carousel_entity' => $carousel_entity->id(), 'carousel_entity_revision' => $vid]));
        }
        else {
          $link = $carousel_entity->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => \Drupal::service('renderer')->renderPlain($username),
              'message' => ['#markup' => $revision->getRevisionLogMessage(), '#allowed_tags' => Xss::getHtmlTagList()],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.carousel_entity.translation_revert', ['carousel_entity' => $carousel_entity->id(), 'carousel_entity_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.carousel_entity.revision_revert', ['carousel_entity' => $carousel_entity->id(), 'carousel_entity_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.carousel_entity.revision_delete', ['carousel_entity' => $carousel_entity->id(), 'carousel_entity_revision' => $vid]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['carousel_entity_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
