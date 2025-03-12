<?php

class Utilisateur{

    private $nom;
    private $prenom;
    private $mdp;

    public function __construct($nom, $prenom, $mdp){
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->mdp = $mdp;
    }

    public function getNom(){
        return $this->nom;
    }
    public function getPrenom(){
        return $this->prenom;
    }
    public function getMdp(){
        return $this->mdp;
    }
    public function setNom($nom){
        $this->nom = $nom;
    }
    public function setPrenom($prenom){
        $this->prenom = $prenom;
    }
    public function setMdp($mdp){
        $this->mdp = $mdp;
    }
}
?>