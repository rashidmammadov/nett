<?php

namespace App\Http\Utility;

class RankingReport {

    private static $id;
    private static $picture;
    private static $username;
    private static $name;
    private static $surname;
    private static $city;
    private static $ranking;
    private static $previousRanking;

    public function __construct($parameters = null) {
        !empty($parameters[IDENTIFIER])         && self::setId($parameters[IDENTIFIER]);
        !empty($parameters[PICTURE])            && self::setPicture($parameters[PICTURE]);
        !empty($parameters[USERNAME])           && self::setUsername($parameters[USERNAME]);
        !empty($parameters[NAME])               && self::setName($parameters[NAME]);
        !empty($parameters[SURNAME])            && self::setSurname($parameters[SURNAME]);
        !empty($parameters[CITY])               && self::setCity($parameters[CITY]);
        !empty($parameters[RANKING])            && self::setRanking($parameters[RANKING]);
        !empty($parameters[PREVIOUS_RANKING])   && self::setPreviousRanking($parameters[PREVIOUS_RANKING]);
    }

    public static function get() {
        return array(
            IDENTIFIER => self::getId(),
            PICTURE => self::getPicture(),
            USERNAME => self::getUsername(),
            NAME => self::getName(),
            SURNAME => self::getSurname(),
            CITY => self::getCity(),
            RANKING => self::getRanking(),
            PREVIOUS_RANKING => self::getPreviousRanking()
        );
    }

    public static function getId() { return self::$id; }

    public static function setId($id): void { self::$id = $id; }

    public static function getPicture() { return self::$picture; }

    public static function setPicture($picture): void { self::$picture = $picture; }

    public static function getUsername() { return self::$username; }

    public static function setUsername($username): void { self::$username = $username; }

    public static function getName() { return self::$name; }

    public static function setName($name): void { self::$name = $name; }

    public static function getSurname() { return self::$surname; }

    public static function setSurname($surname): void { self::$surname = $surname; }

    public static function getCity() { return self::$city; }

    public static function setCity($city): void { self::$city = $city; }

    public static function getRanking() { return self::$ranking; }

    public static function setRanking($ranking): void { self::$ranking = $ranking; }

    public static function getPreviousRanking() { return self::$previousRanking; }

    public static function setPreviousRanking($previousRanking): void { self::$previousRanking = $previousRanking; }


}
