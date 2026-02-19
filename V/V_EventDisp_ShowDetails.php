<div>
    Tous les détails de l'évènement sont affichées ci-dessous; les actions que
    vous pouvez effectuer dépendent des boutons après les zones de texte.
</div>
<?php
echo HTMLElem::formulaire('frmEventDetails','action=eventDisplay&option=toggleSubscriptionStatus',[
    HTMLElem::input('hidden','','txtEventID','txtEventID',$_REQUEST['eventID']),
    HTMLElem::input('text', 'Titre', 'txtTitle', 'txtTitle', $tabEventDetails['title'], ['readonly'=>true,'class'=>'eventTitle']),
    HTMLElem::input('text', 'Type d\'évènement', 'txtEventType', 'txtEventType', $tabEventDetails['eventType'],['readonly'=>true]),
    HTMLElem::input('textarea', 'Description', 'txtDescr', 'txtDescr', $tabEventDetails['description'],['rows'=>3,'readonly'=>true]),
    HTMLElem::input('textarea', 'Description Longue', 'txtDescrL', 'txtDescrL', $tabEventDetails['longDescription'],['rows'=>6,'readonly'=>true]),
    HTMLElem::input('text', 'Date &amp; heure','txtEventDate','txtEventDate',$tabEventDetails['occuringDate'],['readonly'=>true]),
    HTMLElem::input('text', 'Adresse complète','txtEventAddr','txtEventAddr',$tabEventDetails['fullAddress'],['readonly'=>true]),
    HTMLElem::input('text', 'Créé par','txtCreator', 'txtCreator', "$tabEventDetails[creatorLastName] $tabEventDetails[creatorName]",['readonly'=>true]),
    HTMLElem::input('text','Dernières modifications','txtLastEditDate','txtLastEditDate',$tabEventDetails['lastEditDate']?? '(Jamais)',['readonly'=>true]),
    HTMLElem::input('text','Places: Dispo./Total','txtParticipantInfo','txtParticipantInfo',"$tabEventDetails[participantNbCurrent]/$tabEventDetails[participantNbMax]",['readonly'=>true]),
    $userIsSubscribed? HTMLElem::input('submit','','btnSubscribe','btnSubscribe',"Se désinscire de cet évènement"):
    HTMLElem::input('submit','','btnSubscribe','btnSubscribe',"S'inscrire à cet évènement"),
    $createdByUser? HTMLElem::input('submit','','btnEdit','btnEdit',"Modifier l'évènement"): '',
]);