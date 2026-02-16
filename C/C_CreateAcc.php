<?php
$_REQUEST['option'] ??= 'form';

/*
 * Contrôleur pour la partie création de compte de l'application
 */
switch ($_REQUEST['option'])
{
    case 'form':
        $pageTitle = 'Créer un compte';
        $lastName = $name = $eMail = '';
        
        require './V/V_EnTete.php';
        require './V/V_CreateAcc_Form.php';
        break;
    
    case 'checkForm':
        $errorList = [];
        $lastName = filter_input(INPUT_POST,'txtLastName');
        $name = filter_input(INPUT_POST,'txtName');
        $eMail = filter_input(INPUT_POST,'txtEMail');
        $pwd1 = filter_input(INPUT_POST,'txtPassword');
        $pwd2 = filter_input(INPUT_POST,'txtPassword2');
        //Data checks:
        if (!(verifierTexte($lastName) && verifierTexte($name) && verifierFormatEMail($eMail)
                && verifierTexte($pwd1) && verifierTexte($pwd2))
                || $pwd1 != $pwd2)
        {
            //'form'
            $errorList = [verifierTexte($lastName)?: 'Le nom saisi n\'est pas valide.',
                verifierTexte($name)?: 'Le prénom saisi n\est pas valide.',
                verifierFormatEMail($eMail)?: 'Le format de l\'adresse e-mail saisie n\'est pas valide.'];
            
            require './V/V_EnTete.php';
            echo HTMLElem::listeErreurs($errorList);
            require './V/V_CreateAcc_Form.php';
            break;
        }
        else if ($pwd1 != $pwd2)
        {
            //'form'
            $errorList[] = 'Le mot de passe confirmé n\'est pas identique au mot de passe saisi';
            
            require './V/V_EnTete.php';
            echo HTMLElem::listeErreurs($errorList);
            require './V/V_CreateAcc_Form.php';
            break;
        }
        //''
        $pDOPGEE->createUserAcc($lastName,$name,$eMail,password_hash($pwd1,PASSWORD_BCRYPT),PGEEDatabase::UTILISATEUR_ETUDIANT);
        
        require './V/V_EnTete.php';
        require './V/V_CreateAcc_CreationOK.php';
        break;
}