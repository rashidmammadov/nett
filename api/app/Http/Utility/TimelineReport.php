<?php

namespace App\Http\Utility;

class TimelineReport {

    private $gameImage;
    private $gameName;
    private $participantCount;
    private $tournamentRanking;
    private $tournamentId;
    private $startDate;

    public function __construct($parameters = null) {
        $this->setGameImage($parameters[GAME_IMAGE]);
        $this->setGameName($parameters[GAME_NAME]);
        $this->setParticipantCount($parameters[PARTICIPANT_COUNT]);
        $this->setTournamentRanking($parameters[TOURNAMENT_RANKING]);
        $this->setTournamentId($parameters[TOURNAMENT_ID]);
        $this->setStartDate($parameters[START_DATE]);
    }

    public function get() {
        return array(
            GAME_IMAGE => $this->getGameImage(),
            GAME_NAME => $this->getGameName(),
            PARTICIPANT_COUNT => $this->getParticipantCount(),
            TOURNAMENT_RANKING => $this->getTournamentRanking(),
            TOURNAMENT_ID => $this->getTournamentId(),
            START_DATE => $this->getStartDate()
        );
    }

    public function getStartDate() { return $this->startDate; }

    public function setStartDate($startDate): void { $this->startDate = $startDate; }

    public function getGameImage() { return $this->gameImage; }

    public function setGameImage($gameImage): void { $this->gameImage = $gameImage; }

    public function getGameName() { return $this->gameName; }

    public function setGameName($gameName): void { $this->gameName = $gameName; }

    public function getParticipantCount() { return $this->participantCount; }

    public function setParticipantCount($participantCount): void { $this->participantCount = $participantCount; }

    public function getTournamentRanking() { return $this->tournamentRanking; }

    public function setTournamentRanking($tournamentRanking): void { $this->tournamentRanking = $tournamentRanking; }

    public function getTournamentId() { return $this->tournamentId; }

    public function setTournamentId($tournamentId): void { $this->tournamentId = $tournamentId; }

}
