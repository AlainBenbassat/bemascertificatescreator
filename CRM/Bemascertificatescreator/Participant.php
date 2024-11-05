<?php

class CRM_Bemascertificatescreator_Participant {
  public $id;
  public $eventId;
  public $statusId;
  public $firstName;
  public $lastName;
  public $certificateUrl;
  public CRM_Bemascertificatescreator_Event $event;

  public function __construct(int $participantId, CRM_Bemascertificatescreator_Event $event) {
    $this->load($participantId);
    $this->event = $event;
  }

  public function load(int $participantId): void {
    $participant = \Civi\Api4\Participant::get(FALSE)
      ->addSelect('id', 'contact_id', 'event_id', 'status_id', 'contact_id.first_name', 'contact_id.last_name', 'participant_certificate.certificate_url')
      ->addWhere('id', '=', $participantId)
      ->execute()
      ->first();

    if ($participant) {
      $this->id = $participant['id'];
      $this->eventId = $participant['event_id'];
      $this->statusId = $participant['status_id'];
      $this->firstName = $participant['contact_id.first_name'];
      $this->lastName = $participant['contact_id.last_name'];
      $this->certificateUrl = $participant['participant_certificate.certificate_url'];
    }
    else {
      $this->id = 0;
      $this->eventId = 0;
      $this->statusId = 0;
      $this->firstName = '';
      $this->lastName = '';
      $this->certificateUrl = '';
    }
  }

  public function saveCertificate(string $certificateUrl) {
    \Civi\Api4\Participant::update(FALSE)
      ->addValue('participant_certificate.certificate_url', $certificateUrl)
      ->addWhere('id', '=', $this->id)
      ->execute();
  }

  public function toJson() {
$json = <<<EOF
{
  "first_name": "$this->firstName",
  "last_name": "$this->lastName",
  "course_id": "{$this->event->id}",
  "course_code": "{$this->event->code}",
  "course_date": "{$this->event->startDate}"
}

EOF;

    return $json;
  }
}
