<?php
include_once 'VerificationsFormulaires.php';

/**
 * Une classe statique créée pour générer le code HTML de contrôles de saisie et autres balises utiles,
 * prêt à être insérés directement dans une page avec l'instruction "echo".
 */
class HTMLElem
{
    /**
     * Génère un contrôle de saisie sur-mesure;
     * compatible avec tous les types d'"input", ainsi qu'avec les zones de texte (textarea).<br />
     * Place tout dans une "div", avec pour ID celui de l'input avec en plus "divOf*" avant.<br />
     * <i>Exemple: l'ID de la "div" sera "divOfTheElement" si l'ID de l'élément "input" est "theElement"</i>
     */
    public static function input($type,$label,$nom,$iD,$valeur,$tabAttributs = [],$nouvLignesApres = 0,$tabAttributsLabel = [],$nouvLignesApresLabel = 0): string
    {
        $estZoneTexte = $type == 'textarea';
        $code = '<div id="divOf'.ucfirst($iD).'">';
        if (!empty($label))
        {
            $code .= "<label for=\"$iD\"".self::attributs($tabAttributsLabel).">$label</label>".self::sautLigne($nouvLignesApresLabel);
        }
        $code .= ($estZoneTexte? "<$type": "<input type=\"$type\"")." name=\"$nom\" id=\"$iD\"";
        if (!empty($tabAttributs))
        {
            $code .= self::attributs($tabAttributs);
        }
        $code .= ($estZoneTexte? ">$valeur": " value=\"$valeur\"");
        return $code.($estZoneTexte? "</textarea>": ' />').'</div>'.self::sautLigne($nouvLignesApres);
    }

    /**
     * Génère une liste déroulante (select) sur-mesure à partir d'un tableau (Array).<br />
     * L'attribut spécial "selected" peut être inséré dans le tableau des attributs
     * $tabAttributs, et doit prendre comme valeur celle de l'option sélectionnée.<br />
     * Les 2 paramètres $cleOuI* permettent d'expliciter la clé ou l'indice numérique du tableau
     * contenant les informations à placer respectivement comme valeur (attribut "value" de la balise ouvrante "option") et
     * libellé (le texte entre les balises "option"), pour chaque option.
     */
    public static function selectAvecTableau($tableau,$cleOuIValeur,$cleOuITexte,$label,$nom,$iD,$tabAttributs = [],
        $nouvLignesApres = 0,$tabAttributsLabel = [],$nouvLignesApresLabel = 0): string
    {
        $code = '<div id="divOf'.ucfirst($iD).'">';
        $optionSelectionnee = isset($tabAttributs['selected'])? $tabAttributs['selected']: null;
        unset($tabAttributs['selected']);
        if (!empty($label))
        {
            $code .= "<label for=\"$iD\"".self::attributs($tabAttributsLabel).">$label</label>".self::sautLigne($nouvLignesApresLabel);
        }
        $code .= "<select name=\"$nom\" id=\"$iD\"";
        if (!empty($tabAttributs))
        {
            $code .= self::attributs($tabAttributs);
        }
        $code .= '>';
        foreach ($tableau as $ligne)
        {
            $code .= "<option value=\"$ligne[$cleOuIValeur]\""
                . ($ligne[$cleOuIValeur] == $optionSelectionnee? ' selected="selected"': '')
                . ">$ligne[$cleOuITexte]</option>";
        }
        return $code.'</select></div>'.self::sautLigne($nouvLignesApres);
    }


    /**
     * Génère un groupe de cases à cocher, à choix simple (cases "checkbox") ou
     * multiple (boutons "radio"); d'une manière similaire à `self::selectAvecTableau()`, à partir d'un tableau (Array).
     * 
     * L'attribut spécial "selected" doit contenir un tableau, même si un seul élément est contenu.
     * - Dans le cas de checkboxes, ce tableau peut contenir plusieurs éléments.
     * - Sinon, il devra toujours contenir 1 seul élément.
     * 
     * @param boolean $selectionMultiple
     * @param array $tableau
     * @param int|string $cleOuIValeur
     * @param int|string $cleOuITexte
     * @param string $nom
     * @param string $iD
     * @param string $tabAttributs
     * @param int $nouvLignesFinales
     * @param int $nouvLignesApresLabel
     * @param string $titre
     * 
     * @return string
     */
    public static function groupeCasesAvecTableau($selectionMultiple,$tableau,$cleOuIValeur,$cleOuITexte,$nom,$iD,$tabAttributs = [],
        $nouvLignesFinales = 0,$nouvLignesApresLabel = 0,$titre = ''): string
    {
        $code = $titre == ''? '': "<div class=\"labelGroupeBoutons\">$titre</div>";
        $type = $selectionMultiple? 'checkbox': 'radio';
        $optionsSelectionnees[] = isset($tabAttributs['checked'])? $tabAttributs['checked']: null;
        unset($tabAttributs['checked']);
        for ($i = 0; $i < count($tableau); $i++)
        {
            $ligne = $tableau[$i];
            $tabAttributsLigne = $tabAttributs;
            if ((!$selectionMultiple && empty($optionsSelectionnees[0])) && $i == 0)
            {
                $tabAttributsLigne['checked'] = true;
            }
            elseif (in_array($ligne[$cleOuIValeur],$optionsSelectionnees))
            {
                $tabAttributsLigne['checked'] = true;
            }
            $iDUnique = $iD.$i;
            $code .= "<div class=\"boutonOption\"><label for=\"$iDUnique\">$ligne[$cleOuITexte]</label>"
                . self::input($type,'',($selectionMultiple? $nom.'[]': $nom),$iDUnique,$ligne[$cleOuIValeur],$tabAttributsLigne)
                . '</div>'.self::sautLigne($nouvLignesApresLabel);
        }
        return $code.self::sautLigne($nouvLignesFinales);
    }

    /**
     * Génère les attributs d'un contrôle de saisie, les uns à la suite des autres. <br />
     * Se charge de distinguer les types des attributs (booléens, numériques, ...) automatiquement.
     * Un attribut booléen apparaît uniquement si sa valeur est "true".
     * <i>Utilisé dans les fonctions qui générent des contrôles de saisie; à ne pas utiliser tel quel.</i>
     * 
     * @param type $tabAttributs
     * @return string
     */
    private static function attributs($tabAttributs): string
    {
        $code = '';
        foreach ($tabAttributs as $nomAttribut => $valeurAttribut)
        {
            $code .= " $nomAttribut=".($valeurAttribut === true? "\"$nomAttribut\"": "\"$valeurAttribut\"");
        }
        return $code;
    }

    /**
     * Permet d'insérer N sauts de lignes à la suite.
     * 
     * @param int $nombreLignes
     * @return string
     */
    public static function sautLigne($nombreLignes): string
    {
        return str_repeat('<br />',$nombreLignes);
    }

    /**
     * Génère un contrôle de saisie (input) de type Fichier (file). Il doit être
     * précédé d'un autre, de type Caché (hidden) pour définir sa taille max.
     * (Cela n'exclut pas une vérification de la taille des fichiers avec JS ou PHP).
     * 
     * <i>Note: un formulaire qui dispose d'un tel contrôle de saisie doit avoir
     * obligatoirement les attributs `method="post" entype="multipart/form-data"`;
     * cet attribut est automatiquement précisé si le paramètre "contientFichier" de "self::formulaire()"
     * est mis à vrai.</i>
     */
    public static function inputFichier($tailleMaxOctets,$label,$nom,$iD,$valeur,$tabAttributs,$nouvLignesApres = 0,$tabAttributsLabel = [],$nouvLignesApresLabel = 0): string
    {
        return self::input('hidden','','MAX_FILE_SIZE','MAX_FILE_SIZE',$tailleMaxOctets * 8)
            . self::input('file',$label,(isset($tabAttributs['multiple'])? $nom.'[]': $nom),$iD,$valeur,$tabAttributs,$nouvLignesApres,
                $tabAttributsLabel,$nouvLignesApresLabel);
    }

    /**
     * Inclut dans le fichier courant un script JavaScript, provenant d'un fichier/d'un URL;<br />
     * se charge automatiquement du "echo"!
     * 
     * @param type $nomFichierOuURL 
     * @return void
     */
    public static function inclureJS($nomFichierOuURL): void
    {
        echo "<script src=\"$nomFichierOuURL\"></script>";
    }

    /*
     * Génére le code HTML d'un formulaire. Le tableau `$tabInput` doit contenir du code HTML,
     * contrôles de saisie (en l'occurence, générés avec les méthodes comme "self::input()"),
     * ou de simples chaînes de caractère contenant du code HTML.
     */
    public static function formulaire($iD,$destination,$tabInputs,$nouvLignesFinales = 0,$contientFichiers = false,$tabAttributs = []): string
    {
        $code = "<form id=\"$iD\" method=\"post\" action=\"?$destination\""
            . ($contientFichiers? ' enctype="multipart/form-data"': '').self::attributs($tabAttributs).'>';
        foreach ($tabInputs as $hTMLInput)
        {
            $code .= "$hTMLInput\n";
        }
        return $code.'</form>'.self::sautLigne($nouvLignesFinales);
    }

    /**
     * Génère le code HTML d'un lien (balise "a"); celui-ci peut ou non ouvrir un nouvel onglet.
     * 
     * @param string $uRL
     * @param string $texteLien
     * @param int $nouvLignesApres
     * @param bool $nouvOnglet
     * @return string
     */
    public static function lien($uRL,$texteLien,$nouvLignesApres = 0,$nouvOnglet = false): string
    {
        return "<a href=\"$uRL\"".($nouvOnglet? ' target="_blank"': '').">$texteLien</a>".self::sautLigne($nouvLignesApres);
    }

    /**
     * Utilise le tableau `$erreurs` uniquement pour générer la liste (HTML) et ses éléments à afficher sur la page.
     * 
     * @deprecated
     */
    public static function listeErreurs($tabErreurs): string
    {
        $code = '<br /><div id="listeErreur">Les dernières informations saisies ne sont pas valides:<ul>';
        foreach ($tabErreurs as $erreur)
        {
            $code .= $erreur != '1'? "<li>$erreur</li>": '';
        }
        return $code.'</ul></div>';
    }
    
    public static function listePages($nbEnregistrementsRequete,$pageCourante,$uRL): string
    {
        $code = '<div class="pageNb">Affichage: <b>'.($pageCourante * PGEE_EVENT_PER_PAGE + 1)
            . ' ~ '.(($pageCourante+1)*PGEE_EVENT_PER_PAGE > $nbEnregistrementsRequete? $nbEnregistrementsRequete: ($pageCourante+1)*PGEE_EVENT_PER_PAGE)
            . "</b> / $nbEnregistrementsRequete | Aller à la page: <ul>";
        $totalNbPages = (int)($nbEnregistrementsRequete / PGEE_EVENT_PER_PAGE) + ($nbEnregistrementsRequete % PGEE_EVENT_PER_PAGE <= 0? 0: 1);
        for ($pageNb = 0; $pageNb < $totalNbPages; $pageNb++)
        {
            $code .= '<li>'.self::lien("?$uRL&page=$pageNb",$pageNb+1).'</li>';
        }
        return "$code</ul></div>";
    }
    
    public static function eventCard($eventRow): string
    {
        $code = '';
        $cSSClass = 'event';
        $occuredSince = (date_create($eventRow['occuringDate'])->diff(new DateTime('now')))->d;
        $d = date_create($eventRow['occuringDate'])->format('d/m/Y\, H:i');
        if ($occuredSince <= 0)
        {
            $code .= "<div class=\"cardTag\"><b>Expiré:</b> a eu lieu il y a $occuredSince jours ($d)</div>";
            $cSSClass .= 'Done';
        }
        else
        {
            
            $code .= "<div class=\"cardTag\">".($d >= 14? '<b>En approche!</b>': '')
                . " Aura lieu dans $occuredSince jours ($d)</div>";
        }
        if (isset($eventRow['lastEditDate']))
        {
            $d = date_create($eventRow['lastEditDate'])->format('d/m/Y');
            $code .= "<div class=\"cardTag\">Modifié par son auteur ($d)</div>";
        }
        if (isset($eventRow['userIsSubscribed']))
        {
            $code .= '<div class="checkMarkTag">Vous '.($occuredSince <= 0? 'étiez': 'êtes').' inscrit à cet évènement!</div>';
        }
        return "<div class=\"$cSSClass\">"
                . '<div class="eventTitle">'.$eventRow['title']
            . "</div><div>Créé par $eventRow[creatorLastName] $eventRow[creatorName]</div>$eventRow[description]<br />$code"
            . "<div class=\"cardTag\">Places disponibles: $eventRow[participantNbCurrent]/$eventRow[participantNbMax]</div>"
            . self::lien("?action=eventDisplay&option=showDetails&eventID=$eventRow[eventID]",'Actions/Afficher plus...')."</div>";
    }
    
    public static function navbar($userTypeID): string
    {
        //$tabActions = [];
        $code = '<ul id="navbar">';
        switch ($userTypeID)
        {
            case 0: //type étudiant
                $tabActions = ['?action=user&option=logOut'=>'Déconnexion',
                    '?action=user&option=homePage'=>'Page d\'accueil',
                    '?action=eventDisplay&option=all'=>'Afficher tout',
                    '?action=eventDisplay&option=search'=>'Rechercher'];
                break;
            case 1: //type organisateur
                $tabActions = ['?action=user&option=logOut'=>'Déconnexion',
                    '?action=user&option=homePage'=>'Page d\'accueil',
                    '?action=eventDisplay&option=search'=>'Rechercher',
                    '?action=eventDisplay&option=all'=>'Afficher tout',
                    '?action=eventDisplay&option=creation'=>'Créer un évènement',
                    '?action=eventDisplay&option=createdEvents'=>'Gérer vos évènements'];
                break;
            default:
                $tabActions = ['?action=login'=>'Se connecter',
                    '?action=createAcc'=>'Créer un compte &Eacute;tudiant'];
                break;
        }
        foreach ($tabActions as $act => $name)
        {
            $code .= '<li>'.self::lien($act,$name).'</li>';
        }
        return "$code</ul>";
    }
    
}