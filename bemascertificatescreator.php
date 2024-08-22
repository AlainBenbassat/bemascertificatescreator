<?php

require_once 'bemascertificatescreator.civix.php';

use CRM_Bemascertificatescreator_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function bemascertificatescreator_civicrm_config(&$config): void {
  _bemascertificatescreator_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function bemascertificatescreator_civicrm_install(): void {
  _bemascertificatescreator_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function bemascertificatescreator_civicrm_enable(): void {
  _bemascertificatescreator_civix_civicrm_enable();
}
