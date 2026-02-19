<p>
    Cette page contient tous les évènements que vous avez créés.
</p>
<?php
if ($nbCreatedEvents <= 0)
{
    ?>
    <div>
        Il semble que vous n'avez pas encore créé d'évènement.<br />
        Vous pouvez en créer de nouveaux en cliquant sur l'option dédiée sur la barre de navigation.
    </div>
<?php
}
else
{
    $tabCreatedEvents = $pDOPGEE->getEventsCreatedBy(PGEESession::getIDUtilisateur(),$currentPage);
    if (isset($tabCreatedEvents))
    {
        foreach ($tabCreatedEvents as $event)
        {
            echo HTMLElem::eventCard($event);
        }
        echo HTMLElem::listePages($nbCreatedEvents,$currentPage,'action=eventDisplay&option=createdEvents');
    }
}