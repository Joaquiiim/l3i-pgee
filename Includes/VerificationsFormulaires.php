<?php
function verifierFormatEMail($chaine): bool
{
    return isset($chaine) && preg_match('/^([a-z0-9]+(?:[._-][a-z0-9]+)*)@([a-z0-9]+(?:[.-][a-z0-9]+)*\.[a-z]{2,})$/',$chaine);
}

function testerFormatNumero($chaine,$longueurEnCara): bool
{
    return isset($chaine) && preg_match('/[0-9]{'.$longueurEnCara.'}/',$chaine);
}

//Pour tester un numéro sans format particulier, mais dont le nombre de chiffres maximum connu.
function testerFormatNombre($nombre,$longueurEnCara): bool
{
    return isset($nombre) && preg_match("/[0-9]{1,$longueurEnCara}/",$nombre);
}

function verifierTexte($chaine): bool
{
    return isset($chaine) && trim($chaine) != '';
}