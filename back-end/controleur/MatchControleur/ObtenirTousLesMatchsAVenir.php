<?php

require_once '../config/config.php';
require_once '../modele/MatchDAO.php';

class ObtenirTousLesMatchsAVenir{
    private $matchDAO;

    public function __construct()
    {
        $pdo = connectionBD();
        $this->matchDAO = new MatchDAO($pdo);
    }

    public function executer()
    {
        $toutMatchs =$this->matchDAO->selectAll();
        $matchsAVenir = array_filter($toutMatchs, function($match) {
            return strtotime($match['M_date']) > time();
        });
        return $matchsAVenir;


    }
}