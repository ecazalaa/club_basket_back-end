<?php

 class MatchBasket{
     private $Id_match;
     private $m_date;
     private $m_adversaire;
     private $lieu;
     private $resultat;

        public function __construct($m_date, $m_adversaire, $lieu,$resultat = null, $Id_match = null){
            $this->m_date = $m_date;
            $this->m_adversaire = $m_adversaire;
            $this->lieu = $lieu;
            $this->resultat = $resultat;
            $this->Id_match = $Id_match;
        }

        public function getDate(){
            return $this->m_date;
        }
        public function getAdversaire(){
            return $this->m_adversaire;
        }
        public function getLieu(){
            return $this->lieu;
        }
        public function getResultat(){
            return $this->resultat;
        }
        public function getIdMatch(){
            return $this->Id_match;
        }
        public function setDate($m_date){
            $this->m_date = $m_date;
        }
        public function setAdversaire($m_adversaire){
            $this->m_adversaire = $m_adversaire;
        }
        public function setLieu($lieu){
            $this->lieu = $lieu;
        }
        public function setResultat($resultat){
            $this->resultat = $resultat;
        }
 }