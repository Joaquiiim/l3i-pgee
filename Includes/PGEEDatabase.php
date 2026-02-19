<?php
class PGEEDatabase
{
    private static $serveur = PGEE_DB_SERVER;
    private static $nom = PGEE_DB_NAME;
    private static $utilisateur = PGEE_DB_USERNAME;
    private static $mdP = PGEE_DB_PASSWORD;
    private static $port = PGEE_DB_PORT;
    
    private static ?PDO $pDO = null;
    private static ?PGEEDatabase $instance = null;
    
    public const UTILISATEUR_NON_CONNECTE = 'U';
    public const UTILISATEUR_ETUDIANT = 'E';
    public const UTILISATEUR_ORGANISATEUR = 'O';
    
    private function __construct()
    {
        try
        {
            self::$pDO = new PDO('mysql:dbname='.self::$nom.';host='.self::$serveur.self::$port,self::$utilisateur,self::$mdP);
            self::$pDO->query('SET NAMES UTF8');
            self::$pDO->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        catch (PDOException $erreur)
        {
            echo '<marquee>[Erreur Connexion BdD] '.$erreur->getMessage().'</marquee>';
        }
    }

    public function __destruct()
    {
        self::$pDO = null;
    }
    
    public static function getPDO()
    {
        if (self::$instance == null)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getLoginInfos($eMailAddr)
    {
        $sQL = 'SELECT U.u_num AS iD,hash_mdp AS hashMdP,U.tu_num AS userType,CONCAT(nom,\' \',prenom) AS fullName'
            . ' FROM utilisateur U'
            . ' INNER JOIN typeutilisateur T'
            . ' ON U.tu_num = T.tu_num'
            . ' WHERE adresse_email=:AdresseEMail';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':AdresseEMail',$eMailAddr,PDO::PARAM_STR);
        $res->execute();
        return $res->fetch(PDO::FETCH_ASSOC);
    }
    
    public function getEventTypes(): array
    {
        $sQL = 'SELECT te_num AS iD,libelle AS name,u_num AS creatorID'
            . ' FROM TypeEvenement';
        $res = self::$pDO->prepare($sQL);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Renvoie la liste de tous les évènements auquel est inscrit un utilisateur,
     * à partir de l'ID de cet évènement.
     * 
     * @param int $userID
     * @return array
     */
    public function getEventSubscriptions($userID,$pageNb): array
    {
        $sQL = 'SELECT E.e_num AS eventID,E.u_num AS creatorID,nom AS creatorLastName,prenom AS creatorName,'
            . ' E.e_num AS eventID,TE.libelle AS eventType,titre AS title,description,description_longue AS longDescription,'
            . ' date_evenement AS occuringDate,date_derniere_modif AS lastEditDate,nb_places_totales AS participantNbMax,nb_places_dispo AS participantNbCurrent'
            . ' FROM Evenement E'
            . ' INNER JOIN TypeEvenement TE'
            . ' ON E.te_num = TE.te_num'
            . ' INNER JOIN Utilisateur U'
            . ' ON E.u_num = U.u_num'
            . ' INNER JOIN TypeUtilisateur TU'
            . ' ON U.tu_num = TU.tu_num'
            . ' WHERE E.e_num IN'
            . '(SELECT e_num'
            . ' FROM Inscription'
            . ' WHERE u_num = :userID)'
            . ' ORDER BY occuringDate ASC'
            . ' LIMIT :nbPerPage OFFSET :pageNb;';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':userID',$userID,PDO::PARAM_INT);
        $res->bindValue(':nbPerPage',PGEE_EVENT_PER_PAGE,PDO::PARAM_INT);
        $res->bindValue(':pageNb',PGEE_EVENT_PER_PAGE * $pageNb,PDO::PARAM_INT);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEventSubscriptionIDs($userID): array
    {
        $sQL = 'SELECT e_num'
            . ' FROM Inscription'
            . ' WHERE u_num = :userID';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':userID',$userID,PDO::PARAM_INT);
        $res->execute();
        $tab = [];
        $res->setFetchMode(PDO::FETCH_NUM);
        while (($row = $res->fetch()) !== false)
        {
            $tab[] = $row[0];
        }
        return $tab;//$res->fetchAll(PDO::FETCH_NUM);
    }
    
    public function getAllEvents($pageNb): array
    {
        $sQL = 'SELECT E.e_num AS eventID,E.u_num AS creatorID,nom AS creatorLastName,prenom AS creatorName,'
            . ' E.e_num AS eventID,TE.libelle AS eventType,titre AS title,description,description_longue AS longDescription,'
            . ' date_evenement AS occuringDate,date_derniere_modif AS lastEditDate,nb_places_totales AS participantNbMax,nb_places_dispo AS participantNbCurrent'
            . ' FROM Evenement E'
            . ' INNER JOIN TypeEvenement TE'
            . ' ON E.te_num = TE.te_num'
            . ' INNER JOIN Utilisateur U'
            . ' ON E.u_num = U.u_num'
            . ' INNER JOIN TypeUtilisateur TU'
            . ' ON U.tu_num = TU.tu_num'
            . ' ORDER BY occuringDate ASC'
            . ' LIMIT :nbPerPage OFFSET :pageNb;';
        $res = self::$pDO->prepare($sQL);
        $res->bindValue(':nbPerPage',PGEE_EVENT_PER_PAGE,PDO::PARAM_INT);
        $res->bindValue(':pageNb',PGEE_EVENT_PER_PAGE * $pageNb,PDO::PARAM_INT);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getEventCount(): int
    {
        $sQL = 'SELECT COUNT(*) FROM Evenement;';
        $res = self::$pDO->prepare($sQL);
        $res->execute();
        return $res->fetch(PDO::FETCH_NUM)[0];
    }
    
    public function getEventDetails($eventID)
    {
        //eventcardminimalinfos
        $sQL = 'SELECT E.u_num AS creatorID,nom AS creatorLastName,prenom AS creatorName,'
            . ' E.e_num AS eventID,TE.te_num AS eventTypeID,TE.libelle AS eventType,titre AS title,description,description_longue AS longDescription,adresse_evenement AS fullAddress,'
            . ' date_evenement AS occuringDate,date_derniere_modif AS lastEditDate,nb_places_totales AS participantNbMax,nb_places_dispo AS participantNbCurrent'
            . ' FROM Evenement E'
            . ' INNER JOIN TypeEvenement TE'
            . ' ON E.te_num = TE.te_num'
            . ' INNER JOIN Utilisateur U'
            . ' ON E.u_num = U.u_num'
            . ' INNER JOIN TypeUtilisateur TU'
            . ' ON U.tu_num = TU.tu_num'
            . ' WHERE e_num=:eventID;';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
        $res->execute();
        return $res->fetch(PDO::FETCH_ASSOC);
    }
    
        public function getEventsCreatedBy($userID,$pageNb)
    {
        //eventcardminimalinfos
        $sQL = 'SELECT E.u_num AS creatorID,nom AS creatorLastName,prenom AS creatorName,'
            . ' E.e_num AS eventID,TE.libelle AS eventType,titre AS title,description,description_longue AS longDescription,adresse_evenement AS fullAddress,'
            . ' date_evenement AS occuringDate,date_derniere_modif AS lastEditDate,nb_places_totales AS participantNbMax,nb_places_dispo AS participantNbCurrent'
            . ' FROM Evenement E'
            . ' INNER JOIN TypeEvenement TE'
            . ' ON E.te_num = TE.te_num'
            . ' INNER JOIN Utilisateur U'
            . ' ON E.u_num = U.u_num'
            . ' INNER JOIN TypeUtilisateur TU'
            . ' ON U.tu_num = TU.tu_num'
            . ' WHERE U.u_num=:userID;'
            . ' LIMIT :nbPerPage OFFSET :pageNb;';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':userID',$userID,PDO::PARAM_INT);
        $res->bindValue(':nbPerPage',PGEE_EVENT_PER_PAGE,PDO::PARAM_INT);
        $res->bindValue(':pageNb',PGEE_EVENT_PER_PAGE * $pageNb,PDO::PARAM_INT);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getNbEventsCreatedBy($userID)
    {
        $sQL = 'SELECT COUNT(*)'
            . ' FROM Evenement'
            . ' WHERE u_num = :userID';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':userID',$userID,PDO::PARAM_INT);
        $res->execute();
        return $res->fetch(PDO::FETCH_NUM)[0];
    }
    
    
    public function getNbEventSubscriptions($userID): int
    {
        $sQL = 'SELECT COUNT(*)'
            . ' FROM Inscription'
            . ' WHERE u_num = :userID';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':userID',$userID,PDO::PARAM_INT);
        $res->execute();
        return $res->fetch(PDO::FETCH_NUM)[0];
    }
    
    /*
    public function getEventFromSearchQuery($eventTypeID,$pageNb): array
    {
        $sQL = 'SELECT *'
            . ' FROM EventCardMinimalInfos'
            . ' WHERE eventID=:eventTypeID'
            . ' LIMIT :nbPerPage OFFSET :pageNb;';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':eventTypeID',$eventTypeID,PDO::PARAM_INT);
        $res->bindValue(':nbPerPage',PGEE_EVENT_PER_PAGE,PDO::PARAM_INT);
        $res->bindValue(':pageNb',PGEE_EVENT_PER_PAGE * $pageNb,PDO::PARAM_INT);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }*/

    /**
     * Renvoie la liste de tous les utilisateurs inscrits à un certain évènement,
     * dont l'ID est précisé en paramètre.
     * 
     * @param type $eventID
     * @return array
     */
    public function getSubscribedUsers($eventID): array
    {
        $sQL = 'SELECT I.u_num AS iD,nom AS lastName,prenom AS name,adresse_email AS eMailAddr'
            . ' FROM Inscription I'
            . ' INNER JOIN Utilisateur U'
            . ' ON I.u_num = U.u_num'
            . ' WHERE e_num = :eventID;';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
        $res->execute();
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /*
    public function getNbSubscribedUsers($eventID): int
    {
        $sQL = 'SELECT COUNT(*)'
            . ' FROM Inscription'
            . ' WHERE e_num = :eventID;';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
        $res->execute();
        return $res->fetch(PDO::FETCH_NUM)[0];
    }
     */
    
    /**
     * Créer un inscription à un évènement pour un utilisateur. Celle-ci sera
     * datée à l'exécution de cette fonction (qui gère cela automatiquement.
     * 
     * @param int $userID
     * @param int $eventID
     * @return bool Résultat de la requête.
     */
    public function createSubscriptionToEvent($userID,$eventID): bool
    {
        try
        {
            $sQL = 'INSERT INTO Inscription VALUES (:userID,:eventID,:subDate);'
                . ' UPDATE Evenement SET nbPlacesDispo = nbPlacesDispo - 1'
                . ' WHERE e_num=:eventID;';
            $res = self::$pDO->prepare($sQL);
            $res->bindParam(':userID',$userID,PDO::PARAM_INT);
            $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
            $res->bindValue(':subDate',date('Y-m-d H:i:s'),PDO::PARAM_STR);
            $res->execute();
            unset($res);
            $this->operationOnAvailableParticipations('+',$eventID);
        }
        catch (PDOException)
        {
            return false;
        }
        return true;
    }
    
    
    /**
     * Supprime complètement l'inscription à un évènement pour un utilisateur donné.
     * 
     * @param type $userID
     * @param type $eventID
     * @return bool
     */
    public function deleteSubscriptionToEvent($userID,$eventID): bool
    {
        try
        {
            $sQL = 'DELETE FROM Inscription WHERE u_num=:userID AND e_num=:eventID;';
            $res = self::$pDO->prepare($sQL);
            $res->bindParam(':userID',$userID,PDO::PARAM_INT);
            $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
            $res->execute();
            unset($res);
            $this->operationOnAvailableParticipations('-',$eventID);
        }
        catch (PDOException)
        {
            return false;
        }
        return true;
    }
    
    private function operationOnAvailableParticipations($operand,$eventID)
    {
        $sQL = "UPDATE Evenement SET nb_places_dispo = nb_places_dispo $operand 1"
            . ' WHERE e_num=:eventID;';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
        $res->execute();
        unset($res);
    }
    
    /**
     * Insère un nouveau compte utilisateur dans la BdD à partir des informations
     * requises
     * 
     * @param type $lastName
     * @param type $name
     * @param type $eMailAddr
     * @param type $hash
     * @param type $userTypeID
     * @return bool Résultat de la requête.
     */
    public function createUserAcc($lastName,$name,$eMailAddr,$hash,$userTypeID): bool
    {
        try
        {
            $sQL = 'INSERT INTO Utilisateur(nom,prenom,adresse_email,hash_mdp,tu_num) VALUES'
                    . '(:lastName,:name,:eMailAddr,:hash,:userTypeID);';
            $res = self::$pDO->prepare($sQL);
            $res->bindParam(':lastName',$lastName,PDO::PARAM_STR);
            $res->bindParam(':name',$name,PDO::PARAM_STR);
            $res->bindParam(':eMailAddr',$eMailAddr,PDO::PARAM_STR);
            $res->bindParam(':hash',$hash,PDO::PARAM_STR);
            $res->bindParam(':userTypeID',$userTypeID,PDO::PARAM_INT);
            $res->execute();
            unset($res);
        }
        catch (PDOException)
        {
            return false;
        }
        return true;
    }
    
    /**
     * Crée un nouvel évènement.
     * 
     * @param type $date
     * @param type $addr
     * @param type $title
     * @param type $descr
     * @param type $descrL
     * @param type $participantCount
     * @param type $creatorID
     * @param type $eventTypeID
     * @return bool
     */
    public function createEvent($date,$addr,$title,$descr,$descrL,$participantCount,$creatorID,$eventTypeID): bool
    {
        try
        {
            $sQL = 'INSERT INTO Evenement(date_evenement,adresse_evenement,titre,description,description_longue,nb_places_totales,u_num,te_num) VALUES'
                . ' (:date,:addr,:title,:descr,:descrL,:participantCount,:creatorID,:eventTypeID);';
            $res = self::$pDO->prepare($sQL);
            $res->bindParam(':date',$date,PDO::PARAM_STR);
            $res->bindParam(':addr',$addr,PDO::PARAM_STR);
            $res->bindParam(':title',$title,PDO::PARAM_STR);
            $res->bindParam(':descr',$descr,PDO::PARAM_STR);
            $res->bindParam(':descrL',$descrL,PDO::PARAM_STR);
            $res->bindParam(':participantCount',$participantCount,PDO::PARAM_INT);
            $res->bindParam(':creatorID',$creatorID,PDO::PARAM_INT);
            $res->bindParam(':eventTypeID',$eventTypeID,PDO::PARAM_INT);
            $res->execute();
            unset($res);
            
        }
        catch (PDOException)
        {
            return false;
        }
        return true;
    }
    
    
    public function updateEvent($eventID,$date,$addr,$title,$descr,$descrL,$participantCount,$creatorID,$eventTypeID): bool
    {
        try
        {
            $sQL = 'UPDATE Evenement SET'
                . ' date_evenement=:date,adresse_evenement=:addr,titre=:title,description=:descr,'
                . ' description_longue=:descrL,nb_places_totales=:participantCount,te_num=:eventTypeID,'
                . ' date_derniere_modif=:editDate'
                . ' WHERE e_num=:eventID AND u_num=:creatorID;';
            $res = self::$pDO->prepare($sQL);
            $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
            $res->bindParam(':date',$date,PDO::PARAM_STR);
            $res->bindParam(':addr',$addr,PDO::PARAM_STR);
            $res->bindParam(':title',$title,PDO::PARAM_STR);
            $res->bindParam(':descr',$descr,PDO::PARAM_STR);
            $res->bindParam(':descrL',$descrL,PDO::PARAM_STR);
            $res->bindParam(':participantCount',$participantCount,PDO::PARAM_INT);
            $res->bindParam(':creatorID',$creatorID,PDO::PARAM_INT);
            $res->bindParam(':eventTypeID',$eventTypeID,PDO::PARAM_INT);
            $res->bindValue(':editDate',date('Y-m-d H:i:s'),PDO::PARAM_STR);
            $res->execute();
            unset($res);
            
        }
        catch (PDOException)
        {
            return false;
        }
        return true;
    }
    
}