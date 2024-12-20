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
        'location_en' => $this->getJsonField($eventCertificate, 'en', 'course_location'),
        'location_nl' => $this->getJsonField($eventCertificate, 'nl', 'course_location'),
        'location_fr' => $this->getJsonField($eventCertificate, 'fr', 'course_location'),
        'dates_en' => $this->getJsonField($eventCertificate, 'en', 'course_dates'),
        'dates_nl' => $this->getJsonField($eventCertificate, 'nl', 'course_dates'),
        'dates_fr' => $this->getJsonField($eventCertificate, 'fr', 'course_dates'),
      ];
    }
    else {
      $defaultValues = [
        'event_id' => $event->id,
        'event_code' => $event->code,
        'title_en' => $event->titleWithoutCodeEN,
        'title_nl' => $event->titleWithoutCodeNL,
        'title_fr' => $event->titleWithoutCodeFR,
        'location_en' => $event->location,
        'location_nl' => $event->location,
        'location_fr' => $event->location,
        'dates_en' => $event->datesEN,
        'dates_nl' => $event->datesNL,
        'dates_fr' => $event->datesFR,
      ];;
    }

    $this->setDefaults($defaultValues);
  }

  public function postProcess(): void {
    $values = $this->exportValues();
    $event = new CRM_Bemascertificatescreator_Event($values['event_id']);
    $event->titleWithoutCodeEN = $values['title_en'];
    $event->titleWithoutCodeNL = $values['title_nl'];
    $event->titleWithoutCodeFR = $values['title_fr'];
    $event->descriptionEN = $values['description_en'];
    $event->descriptionNL = $values['description_nl'];
    $event->descriptionFR = $values['description_fr'];

    $generator = new CRM_Bemascertificatescreator_Generator();
    $msg = $generator->createForEvent($event);
    CRM_Core_Session::setStatus('', $msg, 'info');
  }

  private function getJsonField(object $eventCertificate, string $lang, string $fieldName): string {
    return $eventCertificate->$lang->$fieldName ?? '';
  }

  private function addFormElements() {
    $this->add('text', 'event_id', 'Evenement ID', ['size' => 20]);
    $this->add('text', 'event_code', 'BEMAS code', ['size' => 20]);

    $this->add('text', 'title_nl', 'Titel opleiding (NL)', ['size' => 80]);
    $this->add('wysiwyg', 'description_nl', 'Omschrijving (NL)', ['cols' => 80]);
    $this->add('text', 'location_nl', 'Locatie (NL)', ['size' => 80]);
    $this->add('text', 'dates_nl', 'Datums (NL)', ['size' => 80]);

    $this->add('text', 'title_fr', 'Titel opleiding (FR)', ['size' => 80]);
    $this->add('wysiwyg', 'description_fr', 'Omschrijving (FR)', ['cols' => 80]);
    $this->add('text', 'location_fr', 'Locatie (FR)', ['size' => 80]);
    $this->add('text', 'dates_fr', 'Datums (FR)', ['size' => 80]);

    $this->add('text', 'title_en', 'Title opleiding (EN)', ['size' => 80]);
    $this->add('wysiwyg', 'description_en', 'Omschrijving (EN)', ['cols' => 80]);
    $this->add('text', 'location_en', 'Locatie (EN)', ['size' => 80]);
    $this->add('text', 'dates_en', 'Datums (EN)', ['size' => 80]);
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
