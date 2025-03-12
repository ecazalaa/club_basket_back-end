<?php

class Joueur {

    // Attributs privés pour encapsuler les données
    private $nom;
    private $prenom;
    private $date_naissance;
    private $taille;
    private $poids;
    private $licence;
    private $statut;

    // Constructeur pour initialiser un joueur
    public function __construct($nom, $prenom, $date_naissance, $taille, $poids, $licence) {
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->date_naissance = $date_naissance;
        $this->taille = $taille;
        $this->poids = $poids;
        $this->licence = $licence;
    }

    // Getters pour accéder aux données
    public function getNom() {
        return $this->nom;
    }
    public function getPrenom() {
        return $this->prenom;
    }
    public function getDateNaissance() {
        return $this->date_naissance;
    }
    public function getTaille() {
        return $this->taille;
    }
    public function getPoids() {
        return $this->poids;
    }
    public function getLicence() {
        return $this->licence;
    }

    public function  getStatut(){
        return $this->statut;
    }
    public function setNom($nom) {
        $this->nom = $nom;
    }
    public function setPrenom($prenom) {
        $this->prenom = $prenom;
    }
    public function setDateNaissance($date_naissance) {
        $this->date_naissance = $date_naissance;
    }
    public function setTaille($taille) {
        $this->taille = $taille;
    }
    public function setPoids($poids) {
        $this->poids = $poids;
    }
    public function setLicence($licence) {
        $this->licence = $licence;
    }

    public function setStatut($statut){
        $this->statut = $statut;
    }




}