<?php

namespace Drupal\selwyn_module\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Bike part entities.
 *
 * @ingroup selwyn_module
 */
interface BikePartInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Bike part name.
   *
   * @return string
   *   Name of the Bike part.
   */
  public function getName();

  /**
   * Sets the Bike part name.
   *
   * @param string $name
   *   The Bike part name.
   *
   * @return \Drupal\selwyn_module\Entity\BikePartInterface
   *   The called Bike part entity.
   */
  public function setName($name);

  /**
   * Gets the Bike part creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Bike part.
   */
  public function getCreatedTime();

  /**
   * Sets the Bike part creation timestamp.
   *
   * @param int $timestamp
   *   The Bike part creation timestamp.
   *
   * @return \Drupal\selwyn_module\Entity\BikePartInterface
   *   The called Bike part entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Bike part published status indicator.
   *
   * Unpublished Bike part are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Bike part is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Bike part.
   *
   * @param bool $published
   *   TRUE to set this Bike part to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\selwyn_module\Entity\BikePartInterface
   *   The called Bike part entity.
   */
  public function setPublished($published);

  /**
   * Gets the Bike part revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Bike part revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\selwyn_module\Entity\BikePartInterface
   *   The called Bike part entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Bike part revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Bike part revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\selwyn_module\Entity\BikePartInterface
   *   The called Bike part entity.
   */
  public function setRevisionUserId($uid);

}
