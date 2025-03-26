<?php
require_once '../config/config.php';
require_once '../modele/ParticiperDAO.php';

class CreerParticipation{
    private $participerDAO;
    private $participation;

    public function __construct(Participer $participation){
        $pdo = connectionBD();
        $this->participerDAO = new ParticiperDAO($pdo);
        $this->participation = $participation;
    }
    public function executer(){
        return $this->participerDAO->insert($this->participation);
    }
}
