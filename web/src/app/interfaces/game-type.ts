export interface GameType {
    gameId?: number | string,
    gameImage: string,
    gameName: string,
    gameType: string,
    platforms?: string[],
    playingType?: string[],
    developer?: string
}
