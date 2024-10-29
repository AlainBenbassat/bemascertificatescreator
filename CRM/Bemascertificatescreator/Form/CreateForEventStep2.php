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

    $this->setFormElementsDefaultValues();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function setFormElementsDefaultValues() {
    $defaultValues = [
      'title_fr' => 'je ne sais pas',
    ];

    $this->setDefaults($defaultValues);
  }

  public function postProcess(): void {
  }

  private function addFormElements() {
    $this->add('text', 'title_nl', 'Titel (NL)');
    $this->add('textarea', 'description_nl', 'Omschrijving (NL)', ['cols' => 80]);

    $this->add('text', 'title_fr', 'Title (FR)');
    $this->add('textarea', 'description_fr', 'Omschrijving (FR)', ['cols' => 80]);

    $this->add('text', 'title_en', 'Title (EN)');
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
