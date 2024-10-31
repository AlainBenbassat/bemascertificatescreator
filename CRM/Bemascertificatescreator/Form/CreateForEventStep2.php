<?php

use CRM_Bemascertificatescreator_ExtensionUtil as E;

class CRM_Bemascertificatescreator_Form_CreateForEventStep2 extends CRM_Core_Form {
  public function buildQuickForm(): void {
    $eventIdFromUrl = $this->getEventId();
    $event = new CRM_Bemascertificatescreator_Event($eventIdFromUrl);
    if ($event->id == 0) {
      CRM_Core_Session::setStatus("Kan evenement met id = $eventIdFromUrl niet vinden", 'Fout', 'error');
      CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/create-certficate-step1'));
      return;
    }

    $this->setTitle($event->title);

    $this->addFormElements();
    $this->addFormButtons();

    $this->setFormElementsDefaultValues($event);

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setFormElementsDefaultValues(CRM_Bemascertificatescreator_Event $event) {
    $certFS = new CRM_Bemascertificatescreator_FileSystem($event->year, $event->code);
    if ($certFS->eventJsonExists()) {
      $eventCertificate = json_decode($certFS->loadEventJson());
      $defaultValues = [
        'event_id' => $event->id,
        'event_code' => $event->code,
        'title_en' => $this->getJsonField($eventCertificate, 'en', 'course_title'),
        'title_nl' => $this->getJsonField($eventCertificate, 'nl', 'course_title'),
        'title_fr' => $this->getJsonField($eventCertificate, 'fr', 'course_title'),
        'description_en' => $this->getJsonField($eventCertificate, 'en', 'course_description'),
        'description_nl' => $this->getJsonField($eventCertificate, 'nl', 'course_description'),
        'description_fr' => $this->getJsonField($eventCertificate, 'fr', 'course_description'),
      ];
    }
    else {
      $defaultValues = [
        'event_id' => $event->id,
        'event_code' => $event->code,
        'title_en' => $event->titleWithoutCodeEN,
        'title_nl' => $event->titleWithoutCodeNL,
        'title_fr' => $event->titleWithoutCodeFR,
      ];;
    }

    $this->setDefaults($defaultValues);
  }

  public function postProcess(): void {
  }

  private function getJsonField(object $eventCertificate, string $lang, string $fieldName): string {
    return $eventCertificate->$lang->$fieldName ?? '';
  }

  private function addFormElements() {
    $this->add('text', 'event_id', 'Evenement ID', ['size' => 20]);
    $this->add('text', 'event_code', 'BEMAS code', ['size' => 20]);

    $this->add('text', 'title_nl', 'Titel opleiding (NL)', ['size' => 80]);
    $this->add('textarea', 'description_nl', 'Omschrijving (NL)', ['cols' => 80]);

    $this->add('text', 'title_fr', 'Titel opleiding (FR)', ['size' => 80]);
    $this->add('textarea', 'description_fr', 'Omschrijving (FR)', ['cols' => 80]);

    $this->add('text', 'title_en', 'Title opleiding (EN)', ['size' => 80]);
    $this->add('textarea', 'description_en', 'Omschrijving (EN)', ['cols' => 80]);
  }

  private function addFormButtons() {
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Save'),
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ]
    ]);
  }

  private function getEventId() {
    return CRM_Utils_Request::retrieve('event_id', 'Positive', $this, TRUE);
  }

  private function getRenderableElementNames(): array {
    $elementNames = [];
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
