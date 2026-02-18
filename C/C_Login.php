<?php
$_REQUEST['option'] ??= 'form';


/*
 * Contrôleur pour la partie de l'application
 * relative à la connexion à un compte utilisateur
 */
switch ($_REQUEST['option'])
{
    case 'form':
        $pageTitle = 'Connexion';
        $lastUsedEMail = '';
        if (PGEESession::estDemarree()
            && PGEESession::getTypeUtilisateur() != PGEESession::UTILISATEUR_NON_CONNECTE)
        {
            header('Location:?action=user');
        }
        require './V/V_EnTete.php';
        require './V/V_Login_Form.php';
        break;
    
    case 'checkForm':
        $pageTitle = 'Connexion';
        $lastUsedPassword = filter_input(INPUT_POST,'txtMdP');
        $lastUsedEMail = filter_input(INPUT_POST,'txtEMail');
        /*if (!(verifierTexte($lastUsedEMail) && verifierTexte($lastUsedPassword)))
        {
            //'form'
            $errorList[] = 'Un identifiant et un MdP sont requis.';

            require './V/V_Login_Form.php';
            echo HTMLElem::listeErreurs($errorList);
        }*/
        $res = $pDOPGEE->getLoginInfos($lastUsedEMail);
        $errorList = [];
        //var_dump($res);
        if ($res) //Exists in DB
        {
            if (password_verify($lastUsedPassword,$res['hashMdP'])) //Password OK
            {
                //'user&homePage'
                PGEESession::setTypeUtilisateur($res['userType']);
                PGEESession::setIDUtilisateur($res['iD']);
                PGEESession::setNomUtilisateur($res['fullName']);
                header('Location:?action=user');
                die();
            }
            else
            {
                //'form'
                $errorList[] = "Mot de passe ou identifiant incorrect.";
                
                require './V/V_EnTete.php';
                echo HTMLElem::listeErreurs($errorList);
                require './V/V_Login_Form.php';
                break;
            }
        }
        else
        {
            //'form'
            $errorList[] = 'Identifiant incorrect.';
            $lastUsedEMail = '';
            
            require './V/V_EnTete.php';
            echo HTMLElem::listeErreurs($errorList);
            require './V/V_Login_Form.php';
            break;
        }
        break;
        
    default:
        $pageTitle = "Page inconnue";
        require_once './V/V_EnTete.php';
        require_once './V/V_UnknownPage.php';
        break;
}