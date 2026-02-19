<div>
    Vous vous apprêtez à modifier les données de l'évènement que vous avez créé.<br />
    Lisez attentivement les instructions en bas, après le formulaire, avant de confirmer.
</div>
<?php
echo HTMLElem::formulaire('frmEventEdition','action=eventDisplay&option=checkEdit',[
    HTMLElem::input('hidden','','txtEventID','txtEventID',$_REQUEST['eventID']),
    HTMLElem::input('text','Titre','txtEventName','txtEventName',$eventTitle,['required'=>true]),
    HTMLElem::selectAvecTableau($tabEventTypes,'iD','name','Type d\'évènement','lstEventType','lstEventType',['selected'=>$eventTypeID]),
    HTMLElem::input('datetime-local','Date &amp; heure','txtEventDate','txtEventDate',$eventDate,['required'=>true,'min'=>date_format(date_create(),'y-m-d\Th:s')]),
    HTMLElem::input('text','Adresse complète','txtFullAddr','txtFullAddr',$eventFullAddr,['required'=>true]),
    HTMLElem::input('textarea','Description','txtDescr','txtDescr',$eventDescr,['required'=>true,'maxlength'=>50,'rows'=>3,'placeholder'=>'Une description brève, telle une phrase d\'accroche']),
    HTMLElem::input('textarea','Description Longue','txtDescrL','txtDescrL',$eventDescrL,['required'=>true,'maxlength'=>500,'rows'=>6,'placeholder'=>'Décrivez votre évènement avec plus de détails ici.']),
    HTMLElem::input('number','Nombre de places disponibles','txtParticipantNb','txtParticipantNb',$eventParticipantNb,['required'=>true,'min'=>0,'max'=>99999]),
    HTMLElem::input('submit','','btnSubmit','btnSubmit','Modifier l\'évènement'),
    HTMLElem::input('submit','','btnCancelEvent','btnCancelEvent','Annuler l\'évènement'),
]);
?>
<div>
    <strong>Attention!</strong> Apporter des modifications à l'évènement
    notifiera jusqu'à <b><?php echo $nbSubscribedUsers ?> utilisateurs</b> inscrits.<br />
    De plus, celui-ci sera <b>marqué comme modifié</b>, avec la date de confirmation
    de ces modifications de votre part.
    <br />
    Marquer un évènement comme annulé ne le supprimera pas; il sera
    déréférencé de la plateforme, mais ses inscrits seront notifiés. Vous
    pourrez changer ce status à tout moment, si besoin.
</div>