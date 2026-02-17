<?php
/**
 * Classe métier utilisée pour gérer les sessions utilisateurs (tableau $_REQUEST)
 * ainsi que toutes les informations récupérées à partir de la BdD.
 */
class PGEESession
{
    public const SESSION_ACTIVE = 1;
    public const SESSION_INACTIVE = 0;
    
    public const UTILISATEUR_NON_CONNECTE = 'N';
    public const UTILISATEUR_ETUDIANT = '0';
    public const UTILISATEUR_ORGANISATEUR = '1';
    
    private static $status = self::SESSION_INACTIVE;
    
    private function __construct()
    {
        session_start();
    }
    
    public static function demarrer(): bool
    {
        $oK = false;
        if (self::$status == self::SESSION_INACTIVE)
        {
            new self(); //?
            self::$status = self::SESSION_ACTIVE;
            $oK = true;
        }
        return $oK;
    }
    
    
    public static function estDemarree(): bool
    {
        return self::$status == self::SESSION_ACTIVE;
    }
    
    public static function fermer(): bool
    {
        $oK = false;
        if (self::$status == self::SESSION_ACTIVE)
        {
            $_SESSION = [];
            self::$status = self::SESSION_INACTIVE;
            $oK = true;
        }
        return $oK;
    }
    
    public static function setTypeUtilisateur(string $typeUtilisateur): void
    {
        $_SESSION['loggedIn']['type'] = $typeUtilisateur;
    }
    
    public static function setIDUtilisateur(int $iDUtilisateur): void
    {
        $_SESSION['loggedIn']['iD'] = $iDUtilisateur;
    }
    
    public static function getNomUtilisateur(): string
    {
        return $_SESSION['loggedIn']['userName']?? 'Utilisateur non connecté.';
    }
    
    public static function setNomUtilisateur($nomUtilisateur): void
    {
        $_SESSION['loggedIn']['userName'] = $nomUtilisateur;
    }
    
    public static function getTypeUtilisateur(): string
    {
        return $_SESSION['loggedIn']['type']?? self::UTILISATEUR_NON_CONNECTE;
    }
    
    public static function getIDUtilisateur(): int
    {
        return isset($_SESSION['loggedIn']['iD'])? $_SESSION['loggedIn']['iD']: -1;
    }
}