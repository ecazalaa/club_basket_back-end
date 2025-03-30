<?php

class ParticiperDAO
{

    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function insert(Participer $participer)
    {
        $query = "INSERT INTO clubbasketbackend_bd.participer(licence,Id_Match, Titu_Remp,poste,Note) VALUES(:licence,:Id_Match ,:Titu_Remp, :poste, :Note)";
        $req = $this->pdo->prepare($query);
        return $req->execute([':licence' => $participer->getLicence(),
            ':Id_Match' => $participer->getIdMatch(),
            ':Titu_Remp' => $participer->getTituRemp(),
            ':poste' => $participer->getPoste(),
            ':Note' => $participer->getNote()]);
    }
    public function select($critere, $motcle)
    {
        $query = "SELECT licence,Id_Match,Titu_Remp,poste,Note FROM clubbasketbackend_bd.participer WHERE $critere = :motcle";
        $req = $this->pdo->prepare($query);
        $req->execute([':motcle' => $motcle]);
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select2($licence, $Id_Match)
    {
        $query = "SELECT licence,Id_Match,Titu_Remp,poste,Note FROM clubbasketbackend_bd.participer WHERE licence = :licence AND Id_Match = :Id_Match";
        $req = $this->pdo->prepare($query);
        $req->execute(['licence' => $licence, 'Id_Match' => $Id_Match]);
        return $req->fetch(PDO::FETCH_ASSOC);
    }

    public function update(Participer $participer)
    {
        $query = "UPDATE clubbasketbackend_bd.participer SET licence = :licence, Id_Match = :Id_Match, Titu_Remp = :Titu_Remp, poste = :poste, Note = :Note WHERE licence = :licence AND Id_Match = :Id_Match";
        $req = $this->pdo->prepare($query);
        return $req->execute([':licence' => $participer->getLicence(),
            ':Id_Match' => $participer->getIdMatch(),
            ':Titu_Remp' => $participer->getTituRemp(),
            ':poste' => $participer->getPoste(),
            ':Note' => $participer->getNote()]);
    }
    public function delete($licence, $Id_Match)
    {
        $query = "DELETE FROM clubbasketbackend_bd.participer WHERE licence = :licence AND Id_Match = :Id_Match";
        $req = $this->pdo->prepare($query);
        return $req->execute(['licence' => $licence, 'Id_Match' => $Id_Match]);
    }
    public function deleteByIdMatch($Id_Match)
    {
        $query = "DELETE FROM clubbasketbackend_bd.participer WHERE Id_Match = :Id_Match";
        $req = $this->pdo->prepare($query);
        return $req->execute(['Id_Match' => $Id_Match]);
    }
    public function updateNote($licence, $Id_Match, $Note)
    {
        $query = "UPDATE clubbasketbackend_bd.participer SET Note = :Note WHERE licence = :licence AND Id_Match = :Id_Match";
        $req = $this->pdo->prepare($query);
        return $req->execute(['Note' => $Note, 'licence' => $licence, 'Id_Match' => $Id_Match]);
    }
}
