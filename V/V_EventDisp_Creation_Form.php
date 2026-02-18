<?php
echo HTMLElem::formulaire('frmEventCreation','action=eventDisplay&option=checkCreation',[
    HTMLElem::input('text','Titre','txtEventName','txtEventName','',['required'=>true]),
    HTMLElem::selectAvecTableau($tabEventTypes,'iD','libelle','Type de l\'évènement','lstEventType','lstEventType'),
    HTMLElem::input('datetime-local','Date & heure','txtEventDate','txtEventDate','',['required'=>true,'min'=>date_format(date(),'y-m-d\Th:s')]),
    HTMLElem::input('textarea','Description','txtDescr','txtDescr','',['required'=>true,'maxlength'=>50,'placeholder'=>'Une description brève, telle une phrase d\accroche']),
    HTMLElem::input('textarea','Description Longue','txtDescrL','txtDescrL','',['required'=>true,'maxlength'=>500,'placeholder'=>'Décrivez votre évènement avec plus de détails ici.']),
    HTMLElem::input('number','Nombre de places disponibles','txtParticipantNb','txtParticipantNb','',['required'=>true,'min'=>0,'max'=>99999]),
    HTMLElem::input('submit','','btnSubmit','btnSubmit','Publier l\'évènement'),
]);
