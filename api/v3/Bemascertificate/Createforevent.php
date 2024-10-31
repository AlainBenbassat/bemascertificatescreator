<?php
use CRM_Bemascertificatescreator_ExtensionUtil as E;

function _civicrm_api3_bemascertificate_Createforevent_spec(&$spec) {
  $spec['event_id']['api.required'] = 1;
}

function civicrm_api3_bemascertificate_Createforevent($params) {
  if (empty($params['event_id']) || !is_int($params['event_id'])) {
    throw new API_Exception('parameter event_id is required and must be an integer', 999);
  }

  try {
    $certificateGenerator = new CRM_Bemascertificatescreator_Generator();
    $event = new CRM_Bemascertificatescreator_Event($params['event_id']);
    $msg = $certificateGenerator->createForEvent($event);

    return civicrm_api3_create_success($msg, $params, 'Bemascertificate', 'Createforevent');
  }
  catch (Exception $e) {
    throw new API_Exception($e->getMessage(), $e->getCode());
  }
}
