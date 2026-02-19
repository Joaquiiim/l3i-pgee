<?php
if ($nbEvents <= 0)
{
    ?>
    <div>
        Las base de données des évènements est vide!
    </div>
<?php
}
else
{
    echo "<div>L'application ".PGEE_APP_NAME." répertorie $nbEvents évènements.</div>";
    $tabEvents = $pDOPGEE->getAllEvents($currentPage);
    if (isset($tabEvents))
    {
        foreach ($tabEvents as $event)
        {
            echo HTMLElem::eventCard($event);
        }
        echo HTMLElem::listePages($nbEvents,$currentPage,'action=eventDisplay&option=all');
    }
}