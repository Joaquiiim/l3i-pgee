<!doctype html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($pageTitle?? "[Titre non-précisé]").' - '.PGEE_APP_NAME;?>: Gestion des évènements étudiants</title>
    <link rel="stylesheet" href="./Styles/AllStyles.css"/>
</head>
<body>
    <div id="logoFull">
        <div><?php echo PGEE_APP_NAME; ?></div>
        <div>Gestion des évènements étudiants</div>
    </div>
    <div id="pageHeader">
        <?php
            echo PGEESession::getNomUtilisateur();
            echo HTMLElem::navbar(PGEESession::getTypeUtilisateur());
            ?>
    </div>
    <div id="pageContent">
        <div id="pageName">
            <?php
            echo $pageTitle?? "";
            ?>
        </div>
