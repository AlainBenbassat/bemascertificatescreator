<?php

class CRM_Bemascertificatescreator_Generator {
  public function __construct() {
    //die("TODO: locatie en trainers in event certificaat toevoegen");
  }

  public function createForEvent(CRM_Bemascertificatescreator_Event $event): string {
    $numParticipantsCreated = 0;

    if ($this->isEventTypeAllowedForCertificate($event->typeId)) {
      $certFS = new CRM_Bemascertificatescreator_FileSystem($event->year, $event->code);
      $certFS->saveEventJson($event->toJson());

      $eventJsonCreated = 'Yes';

      foreach ($event->getParticipantIds() as $participantId) {
        $participant = new CRM_Bemascertificatescreator_Participant($participantId, $event);
        $certificateUrl = $certFS->saveParticipantJson($participant->toJson(), $participant->certificateUrl, $event->languageCode);
        $participant->saveCertificate($certificateUrl);

        $numParticipantsCreated++;
      }

      return "Event json created: $eventJsonCreated, #Participant json created: $numParticipantsCreated";
    }
    else {
      return 'Event json not created. Event type id = ' . $event->typeId . ' cannot have certificates';
    }
  }

  public function createForParticipant(int $participantId): string {
    die('WORK IN PROGRESS');
  }

  public function createForContact(int $contactId): string {
    die('WORK IN PROGRESS');
  }

  private function isEventTypeAllowedForCertificate($eventTypeId) {
    // Not allowed for: invalid = 0, conferentie = 10, Meeting = 17
    if ($eventTypeId == 0 || $eventTypeId == 10 || $eventTypeId == 17) {
      return FALSE;
    }

    return TRUE;
  }


}
