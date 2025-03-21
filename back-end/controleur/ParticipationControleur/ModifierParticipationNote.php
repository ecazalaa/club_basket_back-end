<?php

require_once '../config/config.php';
require_once '../modele/ParticiperDAO.php';

class ModifierParticipationNote{

    private $participerDAO;

    private $licence;
    private $matchId;

    private $note;

    public function __construct($licence, $matchId, $note)
    {
        $pdo = connectionBD();
        $this->participerDAO = new ParticiperDAO($pdo);

        $this->licence = $licence;
        $this->matchId = $matchId;

        $this->note = $note;
    }
    public function executer(){
        return $this->participerDAO->updateNote($this->licence, $this->matchId, $this->note);
    }
}
