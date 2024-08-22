<?php

class CRM_Bemascertificatescreator_Writer {
  private $certificateDirectory = '';

  public function __construct(int $year, string $eventCode) {
    if (!defined('BEMAS_CERTIFICATES_ROOT')) {
      throw new Exception('The constant BEMAS_CERTIFICATES_ROOT must be defined in civicrm.settings.php');
    }

    if (!is_dir(BEMAS_CERTIFICATES_ROOT)) {
      throw new Exception('The directory ' . BEMAS_CERTIFICATES_ROOT . ' does not exist.');
    }

    if (!is_dir(BEMAS_CERTIFICATES_ROOT . "/$year")) {
      mkdir(BEMAS_CERTIFICATES_ROOT . "/$year", 0775);
    }

    if (!is_dir(BEMAS_CERTIFICATES_ROOT . "/$year/$eventCode")) {
      mkdir(BEMAS_CERTIFICATES_ROOT . "/$year/$eventCode", 0775);
    }

    $this->certificateDirectory = BEMAS_CERTIFICATES_ROOT . "/$year/$eventCode";
  }




}
