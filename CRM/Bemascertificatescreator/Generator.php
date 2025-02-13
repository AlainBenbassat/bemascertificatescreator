<?php

class CRM_Bemascertificatescreator_Generator {
  public function __construct() {
  }

  public function createForEvent(CRM_Bemascertificatescreator_Event $event): string {
    $numParticipantsCreated = 0;

    if ($this->isEventTypeAllowedForCertificate($event->typeId)) {
      $certFS = new CRM_Bemascertificatescreator_FileSystem($event->yearFromCourseCode, $event->code);
      $certFS->saveEventJson($event->toJson());

      $eventJsonCreated = 'Yes';

      foreach ($event->getParticipantIds() as $participantId) {
        $participant = new CRM_Bemascertificatescreator_Participant($participantId, $event);
        $certificateUrl = $certFS->saveParticipantJson($participant->toJson(), $participant->certificateUrl, $event->languageCode);
        $participant->saveCertificate($certificateUrl);

        $numParticipantsCreated++;
      }

      return "#Certificates created: $numParticipantsCreated";
    }
    else {
      return 'Cannot create certificates';
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
