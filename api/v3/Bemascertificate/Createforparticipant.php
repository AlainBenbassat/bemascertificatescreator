<?php
use CRM_Bemascertificatescreator_ExtensionUtil as E;

function _civicrm_api3_bemascertificate_Createforparticipant_spec(&$spec) {
  $spec['participant_id']['api.required'] = 1;
}

function civicrm_api3_bemascertificate_Createforparticipant($params) {
  if (empty($params['participant_id']) || !is_int($params['participant_id'])) {
    throw new API_Exception('parameter participant_id is required and must be an integer', 999);
  }

  try {
    $certificateGenerator = new CRM_Bemascertificatescreator_Generator();
    $msg = $certificateGenerator->createForParticipant($params['participant_id']);

    return civicrm_api3_create_success($msg, $params, 'Bemascertificate', 'Createforparticipant');
  }
  catch (Exception $e) {
    throw new API_Exception($e->getMessage(), $e->getCode());
  }
}
