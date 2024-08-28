<?php
use CRM_Bemascertificatescreator_ExtensionUtil as E;

function _civicrm_api3_bemascertificate_Createforcontact_spec(&$spec) {
  $spec['contact_id']['api.required'] = 1;
}

function civicrm_api3_bemascertificate_Createforcontact($params) {
  if (empty($params['contact_id'])) {
    throw new API_Exception('parameter contact_id is required and must be an integer', 999);
  }

  try {
    $certificateGenerator = new CRM_Bemascertificatescreator_Generator();
    $msg = $certificateGenerator->createForContact($params['contact_id']);

    return civicrm_api3_create_success($msg, $params, 'Bemascertificate', 'Createforcontact');
  }
  catch (Exception $e) {
    throw new API_Exception($e->getMessage(), $e->getCode());
  }
}
