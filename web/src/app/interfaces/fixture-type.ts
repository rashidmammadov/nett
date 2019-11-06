import { MatchType } from './match-type';

export interface FixtureType {
    tournamentId: number;
    holderId: number;
    gameId: number;
    tournamentType: string;
    participantCount: number;
    createdAt: string;
    draws?: [{
        drawTitle: string;
        matches?: MatchType[];
    }]
}
