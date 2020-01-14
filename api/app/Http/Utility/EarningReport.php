<?php


namespace App\Http\Utility;


class EarningReport {

    private $earnings;
    private $gameImage;
    private $gameName;
    private $participantCount;
    private $tournamentId;
    private $startDate;
    private $earningPercentage;

    public function __construct($parameters = null) {
        $this->setEarnings($parameters[EARNINGS]);
        $this->setGameImage($parameters[GAME_IMAGE]);
        $this->setGameName($parameters[GAME_NAME]);
        $this->setParticipantCount($parameters[PARTICIPANT_COUNT]);
        $this->setTournamentId($parameters[TOURNAMENT_ID]);
        $this->setStartDate($parameters[START_DATE]);
        $this->setEarningPercentage($parameters[EARNING_PERCENTAGE]);
    }

    public function get() {
        return array(
            EARNINGS => $this->getEarnings(),
            GAME_IMAGE => $this->getGameImage(),
            GAME_NAME => $this->getGameName(),
            PARTICIPANT_COUNT => $this->getParticipantCount(),
            TOURNAMENT_ID => $this->getTournamentId(),
            START_DATE => $this->getStartDate(),
            EARNING_PERCENTAGE => $this->getEarningPercentage()
        );
    }

    public function getEarnings() {  return $this->earnings; }

    public function setEarnings($earnings): void {  $this->earnings = $earnings; }

    public function getGameImage() { return $this->gameImage; }

    public function setGameImage($gameImage): void { $this->gameImage = $gameImage; }

    public function getGameName() { return $this->gameName; }

    public function setGameName($gameName): void { $this->gameName = $gameName; }

    public function getParticipantCount() { return $this->participantCount; }

    public function setParticipantCount($participantCount): void { $this->participantCount = $participantCount; }

    public function getTournamentId() { return $this->tournamentId; }

    public function setTournamentId($tournamentId): void { $this->tournamentId = $tournamentId; }

    public function getStartDate() { return $this->startDate; }

    public function setStartDate($startDate): void { $this->startDate = intval($startDate); }

    public function getEarningPercentage() { return $this->earningPercentage; }

    public function setEarningPercentage($earningPercentage): void { $this->earningPercentage = $earningPercentage; }
 }
