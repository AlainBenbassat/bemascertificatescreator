<?php

class CRM_Bemascertificatescreator_Generator {
  public function createForEvent(int $eventId): string {
    // event ophalen als object
    // alle participants ophalen met contactgegevens
    // loop over participants
    //    certificate genereren
    //    certificaat wegschrijven (checken of pad bestaat bv. 2024/T241008V + checken of basisbestand event bestaat T241008V.json)
    $w = new CRM_Bemascertificatescreator_Writer(2034, 'TESTALAIN');

    return 'ok';
  }

  public function createForParticipant(int $participantId): string {
    return 'ok';
  }

  public function createForContact(int $contactId): string {
    $w = new CRM_Bemascertificatescreator_Writer(2034, 'TESTALAIN');
    
    return 'ok';
  }


}
