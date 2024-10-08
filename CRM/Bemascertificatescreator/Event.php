<?php

class CRM_Bemascertificatescreator_Event {
  public $id;
  public $title;
  public $typeId;
  public $summary;
  public $description;
  public $startDate;
  public $year;
  public $code;

  public function __construct(int $eventId) {
    $this->load($eventId);
  }

  public function load(int $eventId): void {
    $event = \Civi\Api4\Event::get(FALSE)
      ->addSelect('id', 'event_type_id', 'summary', 'title', 'description', 'start_date')
      ->addWhere('id', '=', $eventId)
      ->execute()
      ->first();

    if ($event) {
      $this->id = $event['id'];
      $this->title = $event['title'];
      $this->typeId = $event['event_type_id'];
      $this->summary = $event['summary'];
      $this->description = $event['description'];
      $this->startDate = $event['start_date'];
      $this->year = substr($this->startDate, 0, 4);
      $this->code = $this->extractCodeFromTitle($this->title);
    }
    else {
      $this->id = 0;
      $this->title = '';
      $this->typeId = 0;
      $this->summary = '';
      $this->description = '';
      $this->startDate = '';
      $this->year = 0;
      $this->code = '';
    }
  }

  public function getParticipantIds() {
    $ids = [];

    $participants = \Civi\Api4\Participant::get(FALSE)
      ->addSelect('id')
      ->addWhere('event_id', '=', $this->id)
      ->addWhere('status_id.is_counted', '=', TRUE)
      ->execute();
    foreach ($participants as $participant) {
      $ids[] = $participant['id'];
    }

    return $ids;
  }

  public function toJson() {
    $json = "{\n";
    $json .= '  "course_title": "' . $this->title . "\",\n";
    $json .= '  "course_summary": "' . $this->summary . "\",\n";
    $json .= '  "course_description": "' . $this->description . "\"\n";
    $json .= "}\n";

    return $json;
  }

  private function extractCodeFromTitle(string $title): string {
    $parts = explode(' - ', $title);
    if (count($parts) >= 2) {
      return $parts[0];
    }
    else {
      return '';
    }
  }
}
