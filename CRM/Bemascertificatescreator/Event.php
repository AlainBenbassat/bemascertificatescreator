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
  public array $dates;
  public string $datesEN;
  public string $datesNL;
  public string $datesFR;
  public int $year;
  public string $languageCode;
  public string $location;
  public array $trainers;

  public function __construct(int $eventId) {
    $this->load($eventId);
  }

  public function load(int $eventId): void {
    $event = \Civi\Api4\Event::get(FALSE)
      ->addSelect('*', 'custom.*')
      ->addWhere('id', '=', $eventId)
      ->execute()
      ->first();

    if ($event) {
      $this->id = $event['id'];
      $this->title = $event['title'];
      $this->typeId = $event['event_type_id'];
      $this->description = $event['description'] ?? '';
      $this->dates = $this->getCourseDates($event);
      $this->year = substr($this->dates[0], 0, 4);
      $this->code = $this->extractCodeFromTitle($this->title);
      $this->languageCode = $this->getLanguageFromCode();
      $this->titleWithoutCode = $this->removeCodeFromTitle($this->code, $this->title);
      $this->location = $this->getLocation($event['Opleiding_lesduur.Eventlocatie']);
      $this->trainers = $this->getTrainers($event['id']);

      // get EN title and description
      $event = \Civi\Api4\Event::get(FALSE)
        ->addSelect('title', 'description')
        ->addWhere('id', '=', $eventId)
        ->setLanguage('en_US')
        ->execute()
        ->first();
      $this->titleWithoutCodeEN = $event['title'] ? $this->removeCodeFromTitle($this->code, $event['title']) : $this->titleWithoutCode;
      $this->descriptionEN = $event['description'] ?? $this->description;
      $this->datesEN = $this->convertDatesToString('en');

      // get NL title and description
      $event = \Civi\Api4\Event::get(FALSE)
        ->addSelect('title', 'description')
        ->addWhere('id', '=', $eventId)
        ->setLanguage('nl_NL')
        ->execute()
        ->first();
      $this->titleWithoutCodeNL = $event['title'] ? $this->removeCodeFromTitle($this->code, $event['title']) : $this->titleWithoutCode;
      $this->descriptionNL = $event['description'] ?? $this->description;
      $this->datesNL = $this->convertDatesToString('nl');

      // get FR title and description
      $event = \Civi\Api4\Event::get(FALSE)
        ->addSelect('title', 'description')
        ->addWhere('id', '=', $eventId)
        ->setLanguage('fr_FR')
        ->execute()
        ->first();
      $this->titleWithoutCodeFR = $event['title'] ? $this->removeCodeFromTitle($this->code, $event['title']) : $this->titleWithoutCode;
      $this->descriptionFR = $event['description'] ?? $this->description;
      $this->datesFR = $this->convertDatesToString('fr');
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
      $this->dates = [];
      $this->datesEN = '';
      $this->datesNL = '';
      $this->datesFR = '';
      $this->year = 0;
      $this->code = '';
      $this->titleWithoutCode = '';
      $this->location = '';
      $this->trainers = [];
    }
  }

  public function getParticipantIds() {
    $ids = [];

    $participants = \Civi\Api4\Participant::get(FALSE)
      ->addSelect('id', 'participant_certificate.Geen_certificaat_genereren_voor_deze_persoon')
      ->addWhere('event_id', '=', $this->id)
      ->addWhere('status_id.is_counted', '=', TRUE)
      ->addWhere('role_id', 'IN', [1])
      ->execute();
    foreach ($participants as $participant) {
      if (empty($participant['participant_certificate.Geen_certificaat_genereren_voor_deze_persoon'])) {
        $ids[] = $participant['id'];
      }
    }

    return $ids;
  }

  public function toJson() {
    $courseTrainersJson = '"' . implode('", "', $this->trainers) . '"';

    $descEN = json_encode($this->descriptionEN);
    $descNL = json_encode($this->descriptionNL);
    $descFR = json_encode($this->descriptionFR);

$json = <<<EOF
{
  "en": {
    "course_id": "$this->id",
    "course_code": "$this->code",
    "course_title": "$this->titleWithoutCodeEN",
    "course_description": $descEN,
    "course_dates": "$this->datesEN",
    "course_location": "$this->location",
    "course_trainers": [$courseTrainersJson]
  },
  "nl": {
    "course_id": "$this->id",
    "course_code": "$this->code",
    "course_title": "$this->titleWithoutCodeNL",
    "course_description": $descNL,
    "course_dates": "$this->datesNL",
    "course_location": "$this->location",
    "course_trainers": [$courseTrainersJson]
  },
  "fr": {
    "course_id": "$this->id",
    "course_code": "$this->code",
    "course_title": "$this->titleWithoutCodeFR",
    "course_description": $descFR,
    "course_dates": "$this->datesFR",
    "course_location": "$this->location",
    "course_trainers": [$courseTrainersJson]
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

  private function getLanguageFromCode() {
    $lastChar = substr($this->code, -1);
    if ($lastChar == 'W') {
      return 'fr';
    }
    elseif ($lastChar == 'V') {
      return 'nl';
    }
    else {
      return 'en';
    }
  }

  private function getCourseDates($event) {
    $courseDates = [];

    if (empty($event['Activiteit_status.Datum_dag_1'])) {
      $courseDates[] = $event['start_date'];
    }
    else {
      for ($i = 1; $i <= 6; $i++) {
        if (!empty($event["Activiteit_status.Datum_dag_$i"])) {
          $courseDates[] = $event["Activiteit_status.Datum_dag_$i"];
        }
      }
    }

    return $courseDates;
  }

  private function convertDatesToString(string $lang): string {
    $andWord = [
      'en' => ' and ',
      'nl' => ' en ',
      'fr' => ' et ',
    ];

    if (count($this->dates) == 1) {
      return $this->convertToDateMonthYear($this->dates[0]);
    }

    $tmp = '';
    for ($i = 0; $i < count($this->dates); $i++) {
      if ($i == 0) {
        $tmp = $this->convertToDateMonthYear($this->dates[$i]);
      }
      elseif ($i == count($this->dates) - 1) {
        $tmp .= $andWord[$lang] . $this->convertToDateMonthYear($this->dates[$i]);
      }
      else {
        $tmp .= ', ' . $this->convertToDateMonthYear($this->dates[$i]);;
      }
    }

    return $tmp;
  }

  private function convertToDateMonthYear(string $date): string {
    $d = substr($date, 8, 2);
    $m = substr($date, 5, 2);
    $y = substr($date, 0, 4);
    return "$d/$m/$y";
  }

  private function getLocation($contactId) {
    if (empty($contactId)) {
      return '';
    }

    $contact = \Civi\Api4\Contact::get(FALSE)
      ->addSelect('display_name', 'address_primary.country_id:label', 'address_primary.city', 'address_primary.country_id:abbr')
      ->addWhere('id', '=', $contactId)
      ->execute()
      ->first();

    if (!$contact) {
      return '';
    }

    if (!empty($contact['address_primary.city']) && !empty($contact['address_primary.country_id:abbr'])) {
      return $contact['address_primary.city'] . ' (' . $contact['address_primary.country_id:abbr'] . ')';
    }

    if (!empty($contact['address_primary.city'])) {
      return $contact['address_primary.city'];
    }

    return '';
  }

  private function getTrainers($eventId) {
    $trainers = [];

    $participants = \Civi\Api4\Participant::get(TRUE)
      ->addSelect('contact_id.first_name', 'contact_id.last_name')
      ->addWhere('event_id', '=', $eventId)
      ->addWhere('role_id', 'IN', [4, 6]) // spreker, trainer
      ->execute();
    foreach ($participants as $participant) {
      $trainers[] = $participant['contact_id.first_name'] . ' ' . $participant['contact_id.last_name'];
    }

    return $trainers;
  }
}
