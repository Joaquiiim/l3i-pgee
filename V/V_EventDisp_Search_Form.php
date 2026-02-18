<?php
echo HTMLElem::formulaire('frmEventSearch','',[
    HTMLElem::input('text', 'Termes à rechercher', 'txtSearchTerms', 'txtSearchTerms', '', ['required'=>true]),
    //HTMLElem::input('checkbox','Afficher évèn. expirés?','btnShowExpired','btnShowExpired','e'),
    //HTMLElem::selectAvecTableau($tabTypesEven, $cleOuIValeur, $cleOuITexte, $label, $nom, $iD)
    HTMLElem::input('button','','btnSearch','btnSearch','Rechercher')
],1);
?>
<div id="searchResults">
    
</div>