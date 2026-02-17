<?php
class PGEEDatabase
{
    private static $serveur = PGEE_DB_SERVER;
    private static $nom = PGEE_DB_NAME;
    private static $utilisateur = PGEE_DB_USERNAME;
    private static $mdP = PGEE_DB_PASSWORD;
    private static $port = PGEE_DB_PORT;
    
    private static ?PDO $pDO = null;
    private static ?self $instance = null;
    
    public const UTILISATEUR_NON_CONNECTE = 'U';
    public const UTILISATEUR_ETUDIANT = 'E';
    public const UTILISATEUR_ORGANISATEUR = 'O';
    
    public const OPTION_AUTRE_ID = '***';
    public const OPTION_AUTRE_LIBELLE = '[Autre]';
    
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
     * @param type $userID
     * @return array
     */
    public function getEventSubscriptions($userID,$pageNb): array
    {
        $sQL = 'SELECT *'
            . ' FROM EventCardMinimalInfos'
            . ' WHERE eventID IN'
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
        return $res->fetchAll(PDO::FETCH_NUM);
    }
    
    
    public function getNbEventSubscriptions($userID): int
    {
        $sQL = 'SELECT COUNT(*)'
            . ' FROM EventCardMinimalInfos'
            . ' WHERE eventID IN'
            . '(SELECT e_num'
            . ' FROM Inscription'
            . ' WHERE u_num = :userID)';
        $res = self::$pDO->prepare($sQL);
        $res->bindParam(':userID',$userID,PDO::PARAM_INT);
        $res->execute();
        return $res->fetch(PDO::FETCH_NUM)[0];
    }
    
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
    }

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
            $sQL = 'INSERT INTO Inscription VALUES (:userID,:eventID,:subDate);';
            $res = self::$pDO->prepare($sQL);
            $res->bindParam(':userID',$userID,PDO::PARAM_INT);
            $res->bindParam(':eventID',$eventID,PDO::PARAM_INT);
            $res->bindValue(':subDate',date("Y-m-d H:i:s"),PDO::PARAM_STR);
            $res->execute();
        }
        catch (PDOException)
        {
            return false;
        }
        return true;
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
    
}