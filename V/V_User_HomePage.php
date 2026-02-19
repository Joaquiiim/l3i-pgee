<div>Bienvenue sur <?php echo PGEE_APP_NAME . ', ' . PGEESession::getNomUtilisateur(); ?></div>
<?php
if ($nbUpcomingEvents <= 0)
{
    ?>
    <div>
        Il semble que vous n'êtes inscrits à aucun évènement à venir.<br />
        Vous pouvez en chercher de nouveaux et vous y inscrire, avec l'option Rechercher.
    </div>
<?php
}
else
{
    $tabUpcomingEvents = $pDOPGEE->getEventSubscriptions(PGEESession::getIDUtilisateur(),$currentPage);
    if (isset($tabUpcomingEvents))
    {
        foreach ($tabUpcomingEvents as $event)
        {
            echo HTMLElem::eventCard($event);
        }
        echo HTMLElem::listePages($nbUpcomingEvents,$currentPage,'action=user&option=homePage');
    }
}