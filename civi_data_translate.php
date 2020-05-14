<?php

require_once 'civi_data_translate.civix.php';

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Strings;
use CRM_CiviDataTranslate_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function civi_data_translate_civicrm_config(&$config) {
  _civi_data_translate_civix_civicrm_config($config);
  $dispatcher = Civi::dispatcher();
  $dispatcher->addListener('civi.token.list', ['CRM_CiviDataTranslate_Tokens', 'onListTokens']);
  $dispatcher->addListener('civi.token.eval', ['CRM_CiviDataTranslate_Tokens', 'onEvalTokens']);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function civi_data_translate_civicrm_xmlMenu(&$files) {
  _civi_data_translate_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function civi_data_translate_civicrm_install() {
  _civi_data_translate_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function civi_data_translate_civicrm_postInstall() {
  _civi_data_translate_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function civi_data_translate_civicrm_uninstall() {
  _civi_data_translate_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function civi_data_translate_civicrm_enable() {
  _civi_data_translate_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function civi_data_translate_civicrm_disable() {
  _civi_data_translate_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function civi_data_translate_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _civi_data_translate_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function civi_data_translate_civicrm_managed(&$entities) {
  _civi_data_translate_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function civi_data_translate_civicrm_caseTypes(&$caseTypes) {
  _civi_data_translate_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function civi_data_translate_civicrm_angularModules(&$angularModules) {
  _civi_data_translate_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function civi_data_translate_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _civi_data_translate_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function civi_data_translate_civicrm_entityTypes(&$entityTypes) {
  _civi_data_translate_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function civi_data_translate_civicrm_themes(&$themes) {
  _civi_data_translate_civix_civicrm_themes($themes);
}

/**
 * Implements hook_civicrm_apiWrappers()
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_apiWrappers/
 *
 * @param array $wrappers
 * @param AbstractAction $apiRequest
 *
 * @throws \API_Exception
 */
function civi_data_translate_civicrm_apiWrappers(&$wrappers, $apiRequest) {
  $c = 1;
  $d = method_exists($apiRequest, 'getLanguage');
  // Only implement for apiv4 & not in a circular way.
  if ($apiRequest['entity'] === 'Strings'
    || !$apiRequest instanceof AbstractAction
    || !in_array($apiRequest['action'], ['get', 'create'])
  ) {
    return;
  }

  $apiLanguage = method_exists($apiRequest, 'getLanguage') ? $apiRequest->getLanguage() : ($apiRequest['language'] ?? NULL);
  if (!$apiLanguage || $apiRequest->getLanguage() === Civi::settings()->get('lcMessages')) {
    return;
  }

  if ($apiRequest['action'] === 'create') {
    // Save these cleverly - once it actually reaches this point for create (@todo).
    $strings = civi_data_translate_get_strings_to_set($apiRequest['entity']);
  }
  else {
    if (!isset(\Civi::$statics['cividatatranslate']['translate_fields'][$apiRequest['entity']][$apiRequest->getLanguage()])) {
      $fields = Strings::get()->addWhere('entity_table', '=', CRM_Core_DAO_AllCoreTables::getTableForEntityName($apiRequest['entity']))->setSelect(['entity_field', 'entity_id', 'string'])->execute();
      foreach ($fields as $field) {
        \Civi::$statics['cividatatranslate']['translate_fields'][$apiRequest['entity']][$apiRequest->getLanguage()][$field['entity_id']][$field['entity_field']] = $field['string'];
      }
    }
    if (!empty(\Civi::$statics['cividatatranslate']['translate_fields'][$apiRequest['entity']][$apiRequest->getLanguage()])) {
      $wrappers[] = new CRM_CiviDataTranslate_ApiWrapper(\Civi::$statics['cividatatranslate']['translate_fields'][$apiRequest['entity']][$apiRequest->getLanguage()]);
    }

  }
}

function civi_data_translate_get_strings_to_set($entity) {
  return ['msg_html', 'msg_text', 'subject'];
}


// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function civi_data_translate_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function civi_data_translate_civicrm_navigationMenu(&$menu) {
  _civi_data_translate_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _civi_data_translate_civix_navigationMenu($menu);
} // */
