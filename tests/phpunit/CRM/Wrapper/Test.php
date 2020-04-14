<?php

use CRM_CiviDataTranslate_ExtensionUtil as E;
use Civi\Test\HeadlessInterface;
use Civi\Test\HookInterface;
use Civi\Test\TransactionalInterface;
use Civi\Api4\MessageTemplate;
use Civi\Api4\Strings;

/**
 * FIXME - Add test description.
 *
 * Tips:
 *  - With HookInterface, you may implement CiviCRM hooks directly in the test class.
 *    Simply create corresponding functions (e.g. "hook_civicrm_post(...)" or similar).
 *  - With TransactionalInterface, any data changes made by setUp() or test****() functions will
 *    rollback automatically -- as long as you don't manipulate schema or truncate tables.
 *    If this test needs to manipulate schema or truncate tables, then either:
 *       a. Do all that using setupHeadless() and Civi\Test.
 *       b. Disable TransactionalInterface, and handle all setup/teardown yourself.
 *
 * @group headless
 */
class CRM_Wrapper_Test extends \PHPUnit\Framework\TestCase implements HeadlessInterface, HookInterface, TransactionalInterface {

  /**
   * Headless setup.
   *
   * Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
   * See: https://docs.civicrm.org/dev/en/latest/testing/phpunit/#civitest
   *
   * @return \Civi\Test\CiviEnvBuilder
   * @throws \CRM_Extension_Exception_ParseException
   */
  public function setUpHeadless() {
    //
    return \Civi\Test::headless()
      ->installMe(__DIR__)
      ->apply();
  }

  /**
   * Test that our wrapper interprets locales.
   *
   * @throws \API_Exception
   * @throws \Civi\API\Exception\UnauthorizedException
   */
  public function testMessageTemplateWithWrapper() {
    $template = MessageTemplate::create()->setValues(['msg_html' => 'blah'])->execute()->first();
    Strings::create()->setValues(['entity_table' => 'civicrm_msg_template', 'entity_field' => 'msg_html','entity_id' => $template['id'], 'string' => 'not blah', 'language' => 'fr_FR'])->execute();
    $template = MessageTemplate::get()->addWhere('id', '=', $template['id'])->setSelect(['*'])->setLanguage('fr_FR')->execute()->first();
    $this->assertEquals('not blah', $template['msg_html']);
  }

}
