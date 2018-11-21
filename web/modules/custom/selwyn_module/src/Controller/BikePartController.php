<?php

namespace Drupal\selwyn_module\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Url;
use Drupal\selwyn_module\Entity\BikePartInterface;

/**
 * Class BikePartController.
 *
 *  Returns responses for Bike part routes.
 */
class BikePartController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * Displays a Bike part  revision.
   *
   * @param int $bike_part_revision
   *   The Bike part  revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($bike_part_revision) {
    $bike_part = $this->entityManager()->getStorage('bike_part')->loadRevision($bike_part_revision);
    $view_builder = $this->entityManager()->getViewBuilder('bike_part');

    return $view_builder->view($bike_part);
  }

  /**
   * Page title callback for a Bike part  revision.
   *
   * @param int $bike_part_revision
   *   The Bike part  revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($bike_part_revision) {
    $bike_part = $this->entityManager()->getStorage('bike_part')->loadRevision($bike_part_revision);
    return $this->t('Revision of %title from %date', ['%title' => $bike_part->label(), '%date' => format_date($bike_part->getRevisionCreationTime())]);
  }

  /**
   * Generates an overview table of older revisions of a Bike part .
   *
   * @param \Drupal\selwyn_module\Entity\BikePartInterface $bike_part
   *   A Bike part  object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(BikePartInterface $bike_part) {
    $account = $this->currentUser();
    $langcode = $bike_part->language()->getId();
    $langname = $bike_part->language()->getName();
    $languages = $bike_part->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $bike_part_storage = $this->entityManager()->getStorage('bike_part');

    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $bike_part->label()]) : $this->t('Revisions for %title', ['%title' => $bike_part->label()]);
    $header = [$this->t('Revision'), $this->t('Operations')];

    $revert_permission = (($account->hasPermission("revert all bike part revisions") || $account->hasPermission('administer bike part entities')));
    $delete_permission = (($account->hasPermission("delete all bike part revisions") || $account->hasPermission('administer bike part entities')));

    $rows = [];

    $vids = $bike_part_storage->revisionIds($bike_part);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\selwyn_module\BikePartInterface $revision */
      $revision = $bike_part_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = \Drupal::service('date.formatter')->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $bike_part->getRevisionId()) {
          $link = $this->l($date, new Url('entity.bike_part.revision', ['bike_part' => $bike_part->id(), 'bike_part_revision' => $vid]));
        }
        else {
          $link = $bike_part->link($date);
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
              Url::fromRoute('entity.bike_part.translation_revert', ['bike_part' => $bike_part->id(), 'bike_part_revision' => $vid, 'langcode' => $langcode]) :
              Url::fromRoute('entity.bike_part.revision_revert', ['bike_part' => $bike_part->id(), 'bike_part_revision' => $vid]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.bike_part.revision_delete', ['bike_part' => $bike_part->id(), 'bike_part_revision' => $vid]),
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

    $build['bike_part_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
