<?php

class UtilisateurDAO{


    private $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function insert(Utilisateur $utilisateur){
        $query = "INSERT INTO clubbasket_bd.utilisateur(Nom,Prenom,Mot_de_passe) VALUES(:nom , :prenom, :mdp)";
        $req = $this->pdo->prepare($query);
        return $req->execute([':nom' => $utilisateur->getNom(),
            ':prenom' => $utilisateur->getPrenom(),
            ':mdp' => $utilisateur->getMdp()]);
    }

    public function select($nom, $prenom, $mdp){
        $query = "SELECT nom, prenom,mot_de_passe FROM clubbasket_bd.utilisateur WHERE nom = :nom AND prenom = :prenom AND mot_de_passe = :mdp";
        $req = $this->pdo->prepare($query);
        $req->execute([':nom' => $nom, ':prenom' => $prenom, ':mdp' => $mdp]);
        return $req->fetch(PDO::FETCH_ASSOC);

    }

    // faire un update et un delete
}