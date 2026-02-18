<?php
$_REQUEST['option'] ??= 'form';


/*
 * Contrôleur pour la partie de l'application
 * relative à l'utilisateur et à ses infos, évènements associés...
 */
switch ($_REQUEST['option'])
{
    case 'showDetails':
        if (empty($_REQUEST['eventID']))
        {
            $pageTitle = "Page inconnue";
            require_once './V/V_EnTete.php';
            require_once './V/V_UnknownPage.php';
        }
        else
        {
            $tabEventDetails = $pDOPGEE->getEventDetails($_REQUEST['eventID']);
            $pageTitle = "Informations sur l\'évènement - $tabEventDetails[title]";
            $userIsSubscribed = array_search($_REQUEST['eventID'],$pDOPGEE->getEventSubscriptionIDs(PGEESession::getIDUtilisateur())) !== false;
            require './V/V_EnTete.php';
            require './V/V_EventDisp_ShowDetails.php';
        }
        break;
    
    case 'search':
        $pageTitle = 'Rechercher un évènement';
        break;
    
    case 'manage':
        $pageTitle = 'Gestion de vos évènements';
        break;
    
    case 'edit':
        $pageTitle = 'Modifier un évènement';
        
        break;
    
    case 'checkEdit':
        $pageTitle = 'Modifier un évènement';
        break;
    
    case 'creation':
        $pageTitle = 'Créer un nouvel évènement';
        $tabEventTypes = $pDOPGEE->getEventTypes();
        require './V/V_EnTete.php';
        require './V/V_EventDisp_Creation_Form.php';
        break;
    
    case 'checkCreation':
        $pageTitle = 'Créer un nouvel évènement';
        $errorList = [];
        
        $eventTitle = htmlentities(filter_input(INPUT_POST,'txtEventName'));
        $eventDescr = htmlentities(filter_input(INPUT_POST,'txtDescr'));
        $eventDescrL = htmlentities(filter_input(INPUT_POST,'txtDescrL'));
        $eventDate = date_create_from_format('y-m-d\Th:s',filter_input(INPUT_POST,'txtEventDate'));
        $eventTypeID = filter_input(INPUT_POST,'lstEventType');
        if (!(verifierTexte($eventTitle) && verifierTexte($eventDescr) && verifierTexte($eventDescrL) && !empty($eventDate)))
        {
            //'form'
            $errorList = [verifierTexte($eventTitle)?: 'Le titre saisi n\'est pas valide.',
                verifierTexte($eventDescr)?: 'La description saisie n\est pas valide.',
                verifierTexte($eventDescrL)?: 'La description longue saisie n\est pas valide.',
                empty($eventDate)?: 'Le format de la date n\'est pas valide.'];
        }
        else
        {
            if ($eventDate->diff(date()) < 0)
            {
                $errorList[] = 'La date de l\'évènement à venir est antérieur à la date du jour.';
            }
        }
        if (empty($errorList))
        {
            $pDOPGEE->createEvent();
        }
        break;
    
    case 'toggleSubscriptionStatus':
        $pageTitle = 'Modification du status de l\'inscription...';
        $eventID = filter_input(INPUT_POST,'txtEventID');
        $userIsSubscribed = array_search($eventID,$pDOPGEE->getEventSubscriptionIDs(PGEESession::getIDUtilisateur())) !== false;
        if ($userIsSubscribed)
        {
            //Delete the subscription, because it exists in DB.
            $pDOPGEE->deleteSubscriptionToEvent(PGEESession::getIDUtilisateur(),$eventID);
        }
        else
        {
            //Create the subscription, because it doesn't exist.
            $pDOPGEE->createSubscriptionToEvent(PGEESession::getIDUtilisateur(),$eventID);
        }
        header('Location:?action=user');
        die();
        break;
        
    default:
        $pageTitle = "Page inconnue";
        require_once './V/V_EnTete.php';
        require_once './V/V_UnknownPage.php';
        break;
} 