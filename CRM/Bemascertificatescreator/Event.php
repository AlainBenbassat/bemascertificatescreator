<?php

class CRM_Bemascertificatescreator_Event {
  public int $id;
  public string $code;
  public string $title;
  public string $titleWithoutCode;
  public string $titleWithoutCodeEN;
  public string $titleWithoutCodeNL;
  public string $titleWithoutCodeFR;
  public int $typeId;
  public string $description;
  public string $descriptionEN;
  public string $descriptionNL;
  public string $descriptionFR;
  public string $startDate;
  public int $year;

  public function __construct(int $eventId) {
    $this->load($eventId);
  }

  public function load(int $eventId): void {
    $event = \Civi\Api4\Event::get(FALSE)
      ->addSelect('id', 'event_type_id', 'title', 'description', 'start_date')
      ->addWhere('id', '=', $eventId)
      ->execute()
      ->first();

    if ($event) {
      $this->id = $event['id'];
      $this->title = $event['title'];
      $this->typeId = $event['event_type_id'];
      $this->description = $event['description'] ?? '';
      $this->startDate = $event['start_date'];
      $this->year = substr($this->startDate, 0, 4);
      $this->code = $this->extractCodeFromTitle($this->title);
      $this->titleWithoutCode = $this->removeCodeFromTitle($this->code, $this->title);

      // get EN title and description
      $event = \Civi\Api4\Event::get(FALSE)
        ->addSelect('title', 'description')
        ->addWhere('id', '=', $eventId)
        ->setLanguage('en_US')
        ->execute()
        ->first();
      $this->titleWithoutCodeEN = $event['title'] ? $this->removeCodeFromTitle($this->code, $event['title']) : $this->titleWithoutCode;
      $this->descriptionEN = $event['description'] ?? $this->description;

      // get NL title and description
      $event = \Civi\Api4\Event::get(FALSE)
        ->addSelect('title', 'description')
        ->addWhere('id', '=', $eventId)
        ->setLanguage('nl_NL')
        ->execute()
        ->first();
      $this->titleWithoutCodeNL = $event['title'] ? $this->removeCodeFromTitle($this->code, $event['title']) : $this->titleWithoutCode;
      $this->descriptionNL = $event['description'] ?? $this->description;

      // get FR title and description
      $event = \Civi\Api4\Event::get(FALSE)
        ->addSelect('title', 'description')
        ->addWhere('id', '=', $eventId)
        ->setLanguage('fr_FR')
        ->execute()
        ->first();
      $this->titleWithoutCodeFR = $event['title'] ? $this->removeCodeFromTitle($this->code, $event['title']) : $this->titleWithoutCode;
      $this->descriptionFR = $event['description'] ?? $this->description;
    }
    else {
      $this->id = 0;
      $this->title = '';
      $this->titleWithoutCodeEN = '';
      $this->titleWithoutCodeNL = '';
      $this->titleWithoutCodeFR = '';
      $this->typeId = 0;
      $this->summary = '';
      $this->description = '';
      $this->startDate = '';
      $this->year = 0;
      $this->code = '';
      $this->titleWithoutCode = '';
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
$json = <<<EOF
{
  "en": {
    "course_id": "$this->id",
    "course_code": "$this->code",
    "course_title": "$this->titleWithoutCodeEN",
    "course_description": "$this->descriptionEN"
  },
  "nl": {
    "course_id": "$this->id",
    "course_code": "$this->code",
    "course_title": "$this->titleWithoutCodeNL",
    "course_description": "$this->descriptionNL"
  },
  "fr": {
    "course_id": "$this->id",
    "course_code": "$this->code",
    "course_title": "$this->titleWithoutCodeFR",
    "course_description": "$this->descriptionFR"
  }
}
EOF;

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

  private function removeCodeFromTitle(string $code, string $title) {
    return str_replace($code . ' - ', '', $title);
  }
}
