<?php

class CRM_Bemascertificatescreator_Participant {
  public $id;
  public $eventId;
  public $statusId;

  public function __construct(int $participantId) {
    $this->load($participantId);
  }

  public function load(int $participantId): void {
    $participant = \Civi\Api4\Participant::get(FALSE)
      ->addSelect('id', 'contact_id', 'event_id', 'status_id')
      ->addWhere('id', '=', $participantId)
      ->execute()
      ->first();

    if ($participant) {
      $this->id = $participant['id'];
      $this->eventId = $participant['event_id'];
      $this->statusId = $participant['status_id'];
    }
    else {
      $this->id = 0;
      $this->eventId = 0;
      $this->statusId = 0;
    }
  }

  public function toJson() {
    $json = '{';
    $json .= '  "course_title": "' . $this->title . '",';
    $json .= '  "course_summary": "' . $this->summary . '",';
    $json .= '  "course_description": "' . $this->description . '"';
    $json .= '}';

    return $json;
  }
}
