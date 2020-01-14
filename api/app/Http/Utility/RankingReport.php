<?php

namespace App\Http\Utility;

class RankingReport {

    private $id;
    private $picture;
    private $username;
    private $name;
    private $surname;
    private $city;
    private $ranking;
    private $previousRanking;

    public function __construct($parameters = null) {
        $this->setId($parameters[IDENTIFIER]);
        $this->setPicture($parameters[PICTURE]);
        $this->setUsername($parameters[USERNAME]);
        $this->setName($parameters[NAME]);
        $this->setSurname($parameters[SURNAME]);
        $this->setCity($parameters[CITY]);
        $this->setRanking($parameters[RANKING]);
        $this->setPreviousRanking($parameters[PREVIOUS_RANKING]);
    }

    public function get() {
        return array(
            IDENTIFIER => $this->getId(),
            PICTURE => $this->getPicture(),
            USERNAME => $this->getUsername(),
            NAME => $this->getName(),
            SURNAME => $this->getSurname(),
            CITY => $this->getCity(),
            RANKING => $this->getRanking(),
            PREVIOUS_RANKING => $this->getPreviousRanking()
        );
    }

    public function getId() { return $this->id; }

    public function setId($id): void { $this->id = $id; }

    public function getPicture() { return $this->picture; }

    public function setPicture($picture): void { $this->picture = $picture; }

    public function getUsername() { return $this->username; }

    public function setUsername($username): void { $this->username = $username; }

    public function getName() { return $this->name; }

    public function setName($name): void { $this->name = $name; }

    public function getSurname() { return $this->surname; }

    public function setSurname($surname): void { $this->surname = $surname; }

    public function getCity() { return $this->city; }

    public function setCity($city): void { $this->city = $city; }

    public function getRanking() { return $this->ranking; }

    public function setRanking($ranking): void { $this->ranking = $ranking; }

    public function getPreviousRanking() { return $this->previousRanking; }

    public function setPreviousRanking($previousRanking): void { $this->previousRanking = $previousRanking; }


}
