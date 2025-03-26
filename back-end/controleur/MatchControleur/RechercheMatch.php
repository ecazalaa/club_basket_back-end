<?php

require_once '../config/config.php';
require_once '../modele/MatchDAO.php';
class RechercheMatch{
    private $matchDAO;
    private $critere;
    private $motcle;

    public function __construct($critere, $motcle)
    {
        $pdo = connectionBD();
        $this->matchDAO = new MatchDAO($pdo);
        $this->critere = $critere;
        $this->motcle = $motcle;
    }
    public function executer(){
        return $this->matchDAO->select($this->critere, $this->motcle);
    }

}