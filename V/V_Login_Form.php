<?php
echo HTMLElem::formulaire('frmLogin','action=login&option=checkForm',[
    HTMLElem::input('email','Adresse e-mail','txtEMail','txtEMail',$lastUsedEMail,['required'=>true]),
    HTMLElem::input('password','Mot de passe','txtMdP','txtMdP','',['required'=>true]),
    HTMLElem::input('submit','','btnLogin','btnLogin','Se connecter')
],0,false);
?>
<div class="miscInfos">Pas encore de compte? <a href="?action=createAcc">Créez-en un.</a></div>