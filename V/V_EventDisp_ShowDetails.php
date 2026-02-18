<?php
echo HTMLElem::formulaire('frmEventDetails','action=eventDisplay&option=toggleSubscriptionStatus',[
    HTMLElem::input('hidden','','txtEventID','txtEventID',$_REQUEST['eventID']),
    HTMLElem::input('text', 'Titre', 'txtTitle', 'txtTitle', $tabEventDetails['title'], ['readonly'=>true,'class'=>'eventTitle']),
    HTMLElem::input('text', 'Type d\'évènement', 'txtEventType', 'txtEventType', $tabEventDetails['eventType'],['readonly'=>true]),
    HTMLElem::input('text', 'Date complète','txtEventDate','txtEventDate',$tabEventDetails['occuringDate'],['readonly'=>true]),
    HTMLElem::input('text', 'Adresse complet','txtEventAddr','txtEventAddr',$tabEventDetails['fullAddress'],['readonly'=>true]),
    HTMLElem::input('text', 'Créé par', 'txtCreator', 'txtCreator', "$tabEventDetails[creatorLastName] $tabEventDetails[creatorName]",['readonly'=>true]),
    HTMLElem::input('text','Dernière modification','txtLastEditDate','txtLastEditDate',$tabEventDetails['lastEditDate']?? '(Jamais)',['readonly'=>true]),
    HTMLElem::input('text','Places: Restantes/Total','txtParticipantInfo','txtParticipantInfo',"$tabEventDetails[participantNbCurrent]/$tabEventDetails[participantNbMax]",['readonly'=>true]),
    $userIsSubscribed? HTMLElem::input('submit','','btnSubscribe','btnSubscribe',"Se désinscire de cet évènement"):
    HTMLElem::input('submit','','btnSubscribe','btnSubscribe',"S'inscrire à cet évènement"),
    $createdByUser? HTMLElem::input('submit','','btnEdit','btnEdit',"Modifier l'évènement"): '',
]);