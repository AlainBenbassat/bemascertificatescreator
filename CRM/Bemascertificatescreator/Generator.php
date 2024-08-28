<?php

class CRM_Bemascertificatescreator_Generator {
  public function __construct(private bool $forceCreation = FALSE) {

  }

  public function createForEvent(int $eventId): string {
    $eventJsonCreated = "Event $eventId not found";
    $numParticipantsCreated = 0;

    $event = new CRM_Bemascertificatescreator_Event($eventId);
    if ($event && $this->isEventTypeAllowedForCertificate($event->event_type_id)) {
      $writer = new CRM_Bemascertificatescreator_Writer($event->year, $event->code);

      if (!$writer->eventJsonExists() || $this->forceCreation) {
        $writer->saveEventJson($event->toJson());

        $eventJsonCreated = 'Yes';
      }
      else {
        $eventJsonCreated = 'No, already exists';
      }

      foreach ($event->getParticipantIds() as $participantId) {
        $participant = new CRM_Bemascertificatescreator_Participant($participantId);
        $writer->saveParticipantJson($participant->toJson());

        $numParticipantsCreated++;
      }
    }

    return "Event json created: $eventJsonCreated, #Participant json created: $numParticipantsCreated";
  }

  public function createForParticipant(int $participantId): string {
    $eventJsonCreated = "No! Error ==> particpant $participantId not found";
    $numParticipantsCreated = 0;

    $participant = new CRM_Bemascertificatescreator_Participant($participantId);
    $event = new CRM_Bemascertificatescreator_Event($participant->eventId);
    if ($event && $this->isEventTypeAllowedForCertificate($event->typeId)) {
      $writer = new CRM_Bemascertificatescreator_Writer($event->year, $event->code);

      if (!$writer->eventJsonExists() || $this->forceCreation) {
        $writer->saveEventJson($event->toJson());

        $eventJsonCreated = 'Yes';
      }
      else {
        $eventJsonCreated = 'No, already exists';
      }

      // TODO: contact ophalen en meegeven aan toJson samen met event
      // + rekening houden met participant status
      $participant = new CRM_Bemascertificatescreator_Participant($participantId);
      $writer->saveParticipantJson($participant->toJson());

      $numParticipantsCreated++;
    }

    return "Event json created? $eventJsonCreated, #Participant json created: $numParticipantsCreated";
  }

  public function createForContact(int $contactId): string {
    $w = new CRM_Bemascertificatescreator_Writer(2034, 'TESTALAIN');

    return 'ok';
  }

  private function isEventTypeAllowedForCertificate($eventTypeId) {
    if ($eventTypeId == 17) {
      return FALSE; // Meeting
    }

    return TRUE;
  }


}
