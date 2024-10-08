<?php

class CRM_Bemascertificatescreator_Generator {
  public function __construct(private bool $forceCreation = FALSE) {
  }

  public function createForEvent(int $eventId): string {
    $numParticipantsCreated = 0;

    $event = new CRM_Bemascertificatescreator_Event($eventId);
    if ($this->isEventTypeAllowedForCertificate($event->typeId)) {
      $writer = new CRM_Bemascertificatescreator_Writer($event->year, $event->code);

      if (!$writer->eventJsonExists() || $this->forceCreation) {
        $writer->saveEventJson($event->toJson());

        $eventJsonCreated = 'Yes';
      }
      else {
        $eventJsonCreated = 'No, already exists';
      }

      foreach ($event->getParticipantIds() as $participantId) {
        $participant = new CRM_Bemascertificatescreator_Participant($participantId, $event);
        $writer->saveParticipantJson($participant->toJson());

        $numParticipantsCreated++;
      }

      return "Event json created: $eventJsonCreated, #Participant json created: $numParticipantsCreated";
    }
    else {
      return 'Event json not created. Event type ' . $event->typeId . ' is not valid';
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
