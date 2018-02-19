<?php
/**
 * Created by PhpStorm.
 * User: selwyn
 * Date: 12/10/17
 * Time: 12:02 PM
 */

namespace Drupal\my_entity\Entity;


use Drupal\Core\Config\Entity\ConfigEntityBase;
/**
 * @ConfigEntityType(
 *   id ="announcement",
 *   label = @Translation("Site Announcement"),
 *   handlers = {
 *     "list_builder" = "Drupal\my_entity\SiteAnnouncementListBuilder",
 *     "form" = {
 *       "default" = "Drupal\my_entity\SiteAnnouncementForm",
 *       "add" = "Drupal\my_entity\SiteAnnouncementForm",
 *       "edit" = "Drupal\my_entity\SiteAnnouncementForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     }
 *   },
 *   config_prefix = "announcement",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "delete-form" = "/admin/config/system/site-announcements/manage/{announcement}/delete",
 *     "edit-form" = "/admin/config/system/site-announcements/manage/{announcement}",
 *     "collection" = "/admin/config/system/site-announcements",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "message",
 *   }
 * )
 */
class SiteAnnouncement extends ConfigEntityBase implements SiteAnnouncementInterface {

  /**
   * The announcement's message.
   *
   * @var string
   */
  protected $message;

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->message;
  }
}