<?php

namespace App\Http\Utility;

class MostPlayedReport {

    private $gameId;
    private $gameName;
    private $gameImage;
    private $totalCount;
    private $activeStatusCount;
    private $cancelStatusCount;
    private $closeStatusCount;
    private $openStatusCount;

    public function __construct($parameters = null) {
        $this->setGameId($parameters[GAME_ID]);
        $this->setGameName($parameters[GAME_NAME]);
        $this->setGameName($parameters[GAME_IMAGE]);
        $this->setTotalCount($parameters[TOTAL_COUNT]);
        $this->setActiveStatusCount($parameters[ACTIVE_STATUS_COUNT]);
        $this->setCancelStatusCount($parameters[CANCEL_STATUS_COUNT]);
        $this->setCloseStatusCount($parameters[CLOSE_STATUS_COUNT]);
        $this->setOpenStatusCount($parameters[OPEN_STATUS_COUNT]);
    }

    public function get() {
        return array(
            GAME_ID => $this->getGameId(),
            GAME_NAME => $this->getGameName(),
            GAME_IMAGE => $this->getGameImage(),
            TOTAL_COUNT => $this->getTotalCount(),
            ACTIVE_STATUS_COUNT => $this->getActiveStatusCount(),
            CANCEL_STATUS_COUNT => $this->getCancelStatusCount(),
            CLOSE_STATUS_COUNT => $this->getCloseStatusCount(),
            OPEN_STATUS_COUNT => $this->getOpenStatusCount()
        );
    }

    public function getGameId() { return $this->gameId; }

    public function setGameId($gameId): void { $this->gameId = $gameId; }

    public function getGameName() { return $this->gameName; }

    public function setGameName($gameName): void { $this->gameName = $gameName; }

    public function getGameImage() { return $this->gameImage; }

    public function setGameImage($gameImage): void { $this->gameImage = $gameImage; }

    public function getTotalCount() { return $this->totalCount; }

    public function setTotalCount($totalCount): void { $this->totalCount = $totalCount; }

    public function getActiveStatusCount() { return $this->activeStatusCount; }

    public function setActiveStatusCount($activeStatusCount): void { $this->activeStatusCount = $activeStatusCount; }

    public function getCancelStatusCount() { return $this->cancelStatusCount; }

    public function setCancelStatusCount($cancelStatusCount): void { $this->cancelStatusCount = $cancelStatusCount; }

    public function getCloseStatusCount() { return $this->closeStatusCount; }

    public function setCloseStatusCount($closeStatusCount): void { $this->closeStatusCount = $closeStatusCount; }

    public function getOpenStatusCount() { return $this->openStatusCount; }

    public function setOpenStatusCount($openStatusCount): void { $this->openStatusCount = $openStatusCount; }


}
