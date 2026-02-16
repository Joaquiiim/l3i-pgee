<?php
require_once './UserDefines/Constants.inc.php';
require_once './Includes/PGEESession.php';
require_once './Includes/PGEEDatabase.php';
require_once './Includes/HTML.php';

PGEESession::demarrer();
$pDOPGEE = PGEEDatabase::getPDO();
$_REQUEST['action'] ??= 'login';

/*
 * Point d'entrée unique de l'application
 */
switch ($_REQUEST['action'])
{
    case 'login':
        //$pageTitle = "Connexion";
        require_once './C/C_Login.php';
        break;
    
    case 'createAcc':
        require_once './C/C_CreateAcc.php';
        break;
    
    case 'user':
        require_once './C/C_User.php';
        break;
    
    case 'eventDisplay':
        break;
    
    default:
        $pageTitle = "Page inconnue";
        require_once './V/V_EnTete.php';
        require_once './V/V_PageNotFound.php';
}
require_once './V/V_PiedPage.php';
