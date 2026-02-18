<?php
echo HTMLElem::formulaire('frmEventCreation','action=eventDisplay&option=checkCreation',[
    HTMLElem::input('text','Titre','txtEventName','txtEventName',$eventTitle,['required'=>true]),
    HTMLElem::selectAvecTableau($tabEventTypes,'iD','name','Type de l\'évènement','lstEventType','lstEventType',['selected'=>$eventTypeID]),
    HTMLElem::input('datetime-local','Date & heure','txtEventDate','txtEventDate','',['required'=>true,'min'=>date_format(date_create(),'y-m-d\Th:s')]),
    HTMLElem::input('text','Adresse complète','txtFullAddr','txtFullAddr',$eventFullAddr,['required'=>true]),
    HTMLElem::input('textarea','Description','txtDescr','txtDescr',$eventDescr,['required'=>true,'maxlength'=>50,'rows'=>3,'placeholder'=>'Une description brève, telle une phrase d\'accroche']),
    HTMLElem::input('textarea','Description Longue','txtDescrL','txtDescrL',$eventDescrL,['required'=>true,'maxlength'=>500,'rows'=>6,'placeholder'=>'Décrivez votre évènement avec plus de détails ici.']),
    HTMLElem::input('number','Nombre de places disponibles','txtParticipantNb','txtParticipantNb',$eventParticipantNb,['required'=>true,'min'=>0,'max'=>99999]),
    HTMLElem::input('submit','','btnSubmit','btnSubmit','Publier l\'évènement'),
]);
