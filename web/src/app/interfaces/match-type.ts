import { ParticipantType } from './participant-type';

export interface MatchType {
    tourId: number;
    matchId: number;
    available: boolean;
    home?: ParticipantType;
    away?: ParticipantType;
    winner?: ParticipantType;
    loser?: ParticipantType;
    date?: string;
    updatedAt?: string;
}
