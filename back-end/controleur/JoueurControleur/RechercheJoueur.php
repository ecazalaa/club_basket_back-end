<?php
require_once '../config/config.php';
require_once '../modele/JoueurDAO.php';
class RechercheJoueur{
    private $joueurDAO;
    private $critere;
    private $motcle;
    public function __construct($critere, $motcle)
    {
        $pdo = connectionBD();
        $this->joueurDAO = new JoueurDAO($pdo);
        $this->critere = $critere;
        $this->motcle = $motcle;
    }
    public function executer(){
        return $this->joueurDAO->select($this->critere, $this->motcle);
    }


}