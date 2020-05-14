<?php

/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

namespace Civi\Api4;

/**
 * MsgTemplate entity.
 *
 * This is a collection of MsgTemplate, for reuse in import, export, etc.
 *
 * @package Civi\Api4
 */
class Message {

  /**
   * @return \Civi\Api4\Action\Message\Render
   *
   * @throws \API_Exception
   */
  public static function render() {
    return new Action\Message\Render(__CLASS__, __FUNCTION__);
  }

}
