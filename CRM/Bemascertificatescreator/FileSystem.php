<?php

class CRM_Bemascertificatescreator_FileSystem {
  public $certificateDirectory = '';

  public function __construct(public int $year, public string $eventCode) {
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

  public function eventJsonExists() {
    if (is_file($this->certificateDirectory . '/' . $this->eventCode . '.json')) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  public function saveEventJson(string $json): string {
    $path = $this->certificateDirectory . '/' . $this->eventCode . '.json';
    $this->writeFile($path, $json);
    return $path;
  }

  public function loadEventJson() {
    $path = $this->certificateDirectory . '/' . $this->eventCode . '.json';
    return $this->readFile($path);
  }

  public function saveParticipantJson(string $json): string {
    $path = $this->certificateDirectory . '/' . $this->getGUID() . '.json';
    $this->writeFile($path, $json);
    return $path;
  }

  private function writeFile(string $fileName, string $content) {
    $stream = fopen($fileName, 'w');
    fwrite($stream, $content);
    fclose($stream);
  }

  private function readFile(string $fileName) {
    return file_get_contents($fileName);
  }

  private function getGUID() {
    // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
    $data = random_bytes(16);

    // Set version to 0100
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    // Set bits 6-7 to 10
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    // Output the 36 character UUID.
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
  }


}
