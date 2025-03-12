<?php

require_once '../config/config.php';
require_once '../modele/ParticiperDAO.php';

class SupprimerParticipation{
    private $participerDAO;

    public function __construct()
    {
        $pdo = connectionBD();
        $this->participerDAO = new ParticiperDAO($pdo);
    }

    public function executer($licence, $idMatch){
        return $this->participerDAO->delete($licence, $idMatch);
    }
}