<?php

class Participer{

    private $licence;
    private $id_match;
    private $tituRemp;
    private $poste;
    private $note;

    //attention a note = null
    public function __construct($licence, $id_match, $tituRemp, $poste, $note = null){
        $this->licence = $licence;
        $this->id_match = $id_match;
        $this->tituRemp = $tituRemp;
        $this->poste = $poste;
        $this->note = $note;
    }
    public function getLicence(){
        return $this->licence;
    }
    public function getIdMatch(){
        return $this->id_match;
    }
    public function getTituRemp(){
        return $this->tituRemp;
    }
    public function getPoste(){
        return $this->poste;
    }
    public function getNote(){
        return $this->note;
    }
    public function setLicence($licence){
        $this->licence = $licence;
    }
    public function setIdMatch($id_match){
        $this->id_match = $id_match;
    }
    public function setTituRemp($tituRemp){
        $this->tituRemp = $tituRemp;
    }
    public function setPoste($poste){
        $this->poste = $poste;
    }
    public function setNote($note){
        $this->note = $note;
    }

}