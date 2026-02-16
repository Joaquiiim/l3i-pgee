<?php
echo HTMLElem::formulaire('frmCreateAcc', 'action=createAcc&option=checkForm',[
    HTMLElem::input('text','Nom','txtLastName','txtLastName','',['required'=>true]),
    HTMLElem::input('text','Prénom','txtName','txtName','',['required'=>true]),
    HTMLElem::input('email','Adresse e-mail','txtEMail','txtEMail','',['required'=>true]),
    HTMLElem::input('password','Mot de passe','txtPassword','txtPassword','',['required'=>true]),
    HTMLElem::input('password','Confirmer mot passe','txtPassword2','txtPassword2','',['required'=>true]),
    HTMLElem::input('submit','','btnSubmit','btnSubmit','Créer le compte'),
],0,false);
?>
<div class="miscInfos">Vous disposez déjà d'un identifiant? <a href="?action=login">Connectez-vous ici</a></div>