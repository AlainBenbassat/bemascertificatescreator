<?php

use CRM_Bemascertificatescreator_ExtensionUtil as E;

class CRM_Bemascertificatescreator_Form_CreateForEventStep1 extends CRM_Core_Form {
  public function buildQuickForm(): void {
    $this->setTitle('Maak certificaten voor evenement');

    $this->addFormElements();
    $this->addFormButtons();

    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  public function postProcess(): void {
    $values = $this->exportValues();
    CRM_Utils_System::redirect(CRM_Utils_System::url('civicrm/create-certficate-step2', 'event_id=' . $values['event_id']));
  }

  private function addFormElements() {
    $this->add('text', 'event_id', 'Evenement ID', null, TRUE);
  }

  private function addFormButtons() {
    $this->addButtons([
      [
        'type' => 'submit',
        'name' => 'Volgende &gt;',
        'isDefault' => TRUE,
      ],
      [
        'type' => 'cancel',
        'name' => E::ts('Cancel'),
      ]
    ]);
  }

  public function getRenderableElementNames(): array {
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
