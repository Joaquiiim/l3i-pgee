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
            $createdByUser = $tabEventDetails['creatorID'] == PGEESession::getIDUtilisateur();
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
        if (empty($_REQUEST['eventID']))
        {
            $pageTitle = "Page inconnue";
            require_once './V/V_EnTete.php';
            require_once './V/V_UnknownPage.php';
        }
        else
        {
            $tabEventDetails = $pDOPGEE->getEventDetails($_REQUEST['eventID']);
            $pageTitle = "Modifier l\'évènement - $tabEventDetails[title]";
        }
        break;
    
    case 'checkEdit':
        $pageTitle = 'Modifier un évènement';
        break;
    
    case 'creation':
        $pageTitle = 'Créer un nouvel évènement';
        $tabEventTypes = $pDOPGEE->getEventTypes();
        $eventTitle = $eventDescr = $eventDescrL = $eventFullAddr = $eventDate = $eventTypeID = $eventParticipantNb = '';
        require './V/V_EnTete.php';
        require './V/V_EventDisp_Creation_Form.php';
        break;
    
    case 'checkCreation':
        $pageTitle = 'Créer un nouvel évènement';
        $errorList = [];
        
        $eventTitle = htmlentities(filter_input(INPUT_POST,'txtEventName'));
        $eventDescr = htmlentities(filter_input(INPUT_POST,'txtDescr'));
        $eventDescrL = htmlentities(filter_input(INPUT_POST,'txtDescrL'));
        $eventFullAddr = htmlentities(filter_input(INPUT_POST,'txtFullAddr'));
        $eventDate = date_create_from_format('Y-m-d?H:i',$_REQUEST['txtEventDate']);
        $eventTypeID = filter_input(INPUT_POST,'lstEventType');
        $eventParticipantNb = htmlspecialchars(filter_input(INPUT_POST,'txtParticipantNb'));
        
        if (!(verifierTexte($eventTitle) && verifierTexte($eventDescr) && verifierTexte($eventDescrL)
                && verifierNombre($eventParticipantNb,5)))// && !empty($eventDate)))
        {
            //'form'
            $errorList = [verifierTexte($eventTitle)?: 'Le titre saisi n\'est pas valide.',
                verifierTexte($eventDescr)?: 'La description saisie n\'est pas valide.',
                verifierTexte($eventDescrL)?: 'La description longue saisie n\'est pas valide.',
                verifierTexte($eventFullAddr)?: 'L\'addresse saisie n\'est pas valide.',
                verifierNombre($eventParticipantNb,5)?: 'Le nombre de participant est invalide: saisissez un nombre entier',
                empty($eventDate)?: 'Le format de la date n\'est pas valide.'];
        }
        else
        {
            if ($eventDate->diff(date_create('now'))->d < 0)
            {
                $errorList[] = 'La date de l\'évènement à venir est antérieur à la date du jour.';
            }
        }
        
        if (empty($errorList))
        {
            $pDOPGEE->createEvent($eventDate);
            header('Location:?action=eventDisplay&option=createdEvents');
            die();
        }
        else
        {
            //creation
            $pageTitle = 'Créer un nouvel évènement';
            $tabEventTypes = $pDOPGEE->getEventTypes();
            $eventTitle = $eventDescr = $eventDescrL = $eventDate = $eventTypeID = $eventParticipantNb = '';
            require './V/V_EnTete.php';
            echo HTMLElem::listeErreurs($errorList);
            require './V/V_EventDisp_Creation_Form.php';
            break;
        }
        break;
    
    case 'toggleSubscriptionStatus':
        $pageTitle = 'Modification du status de l\'inscription...';
        $eventID = filter_input(INPUT_POST,'txtEventID');
        $userIsSubscribed = array_search($eventID,$pDOPGEE->getEventSubscriptionIDs(PGEESession::getIDUtilisateur())) !== false;
        if (isset($_REQUEST['btnEdit']))
        {
            header("Location:?action=user&option=edit&eventID=$eventID");
        }
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