<?php

class CRM_Bemascertificatescreator_Config {
  private static $singleton;

  private $cachedParticipantCertificateCustomGroup;
  private $cachedParticipantCertificateUrlCustomField;

  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new self();
    }
    return self::$singleton;
  }

  public function create() {
    $this->getParticipantCertificateCustomGroup();
    $this->getParticipantCertificateUrlCustomField();
  }

  public function getParticipantCertificateCustomGroup() {
    if (empty($this->cachedParticipantCertificateCustomGroup)) {
      $this->loadParticipantCertificateCustomGroup();
    }

    return $this->cachedParticipantCertificateCustomGroup;
  }

  public function getParticipantCertificateUrlCustomField() {
    if (empty($this->cachedParticipantCertificateUrlCustomField)) {
      $this->loadParticipantCertificateUrlCustomField();
    }

    return $this->cachedParticipantCertificateUrlCustomField;
  }

  private function loadParticipantCertificateCustomGroup() {
    $this->cachedParticipantCertificateCustomGroup = \Civi\Api4\CustomGroup::get(FALSE)
      ->addWhere('name', '=', 'participant_certificate')
      ->execute()
      ->first();

    if (!$this->cachedParticipantCertificateCustomGroup) {
      $this->cachedParticipantCertificateCustomGroup = \Civi\Api4\CustomGroup::create(FALSE)
        ->setLanguage('en_US')
        ->addValue('name', 'participant_certificate')
        ->addValue('title', 'Certificate')
        ->addValue('extends', 'Participant')
        ->addValue('is_active', TRUE)
        ->addValue('collapse_display', TRUE)
        ->addValue('is_public', FALSE)
        ->addValue('table_name', 'civicrm_value_participant_certificate')
        ->execute()[0];

      \Civi\Api4\CustomGroup::update(FALSE)
        ->setLanguage('nl_NL')
        ->addValue('title', 'Certificaat')
        ->addWhere('id', '=', $this->cachedParticipantCertificateCustomGroup['id'])
        ->execute();

      \Civi\Api4\CustomGroup::update(FALSE)
        ->setLanguage('fr_FR')
        ->addValue('title', 'Certificat')
        ->addWhere('id', '=', $this->cachedParticipantCertificateCustomGroup['id'])
        ->execute();
    }
  }

  private function loadParticipantCertificateUrlCustomField() {
    $this->cachedParticipantCertificateUrlCustomField = \Civi\Api4\CustomField::get(FALSE)
      ->addWhere('name', '=', 'certificate_url')
      ->execute()
      ->first();

    if (!$this->cachedParticipantCertificateUrlCustomField) {
      $this->cachedParticipantCertificateUrlCustomField = \Civi\Api4\CustomField::create(FALSE)
        ->setLanguage('en_US')
        ->addValue('custom_group_id', $this->getParticipantCertificateCustomGroup()['id'])
        ->addValue('name', 'certificate_url')
        ->addValue('label', 'Certificate URL')
        ->addValue('data_type', 'Link')
        ->addValue('html_type', 'Link')
        ->addValue('is_active', TRUE)
        ->execute()[0];

      \Civi\Api4\CustomField::update(FALSE)
        ->setLanguage('nl_NL')
        ->addValue('label', 'URL naar certificaat')
        ->addWhere('id', '=', $this->cachedParticipantCertificateUrlCustomField['id'])
        ->execute();

      \Civi\Api4\CustomField::update(FALSE)
        ->setLanguage('fr_FR')
        ->addValue('label', 'Lien vers certificat')
        ->addWhere('id', '=', $this->cachedParticipantCertificateUrlCustomField['id'])
        ->execute();
    }
  }
}
