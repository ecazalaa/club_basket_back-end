<?php
require_once '../config/config.php';
require_once '../modele/JoueurDAO.php';
class SupprimerJoueur{

    private $joueurDAO;

    public function __construct()
    {
        $pdo = connectionBD();
        $this->joueurDAO = new JoueurDAO($pdo);
    }

    public function executer($licence){
        return $this->joueurDAO->delete($licence);
    }
}