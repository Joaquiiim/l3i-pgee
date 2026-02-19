<?php
$_REQUEST['option'] ??= 'homePage';


/*
 * Contrôleur pour la partie de l'application
 * relative à l'utilisateur et à ses infos, évènements associés...
 */
if (PGEESession::getTypeUtilisateur() != PGEESession::UTILISATEUR_NON_CONNECTE)
    {
    switch ($_REQUEST['option'])
    {
        case 'homePage':
            $_REQUEST['page'] ??= 0;
            $currentPage = &$_REQUEST['page'];
            $pageTitle = 'Page d\'accueil - Vos inscriptions';
            $nbUpcomingEvents = $pDOPGEE->getNbEventSubscriptions(PGEESession::getIDUtilisateur());
            require './V/V_EnTete.php';
            require './V/V_User_HomePage.php';
            break;

//        case 'settings':
//            $pageTitle = 'Paramètres du compte';
//            
//            require './V/V_EnTete.php';
//            break;

        case 'logOut':
            $pageTitle = 'Déconnexion en cours...';
            if (PGEESession::estDemarree())
            {
                PGEESession::fermer();
                header('Location:?');
            }
            break;
        
        default:
            $pageTitle = "Page inconnue";
            require_once './V/V_EnTete.php';
            require_once './V/V_UnknownPage.php';
            break;
    }
}
else
{
    $pageTitle = "Page inconnue";
    require_once './V/V_EnTete.php';
    require_once './V/V_UnknownPage.php';
}