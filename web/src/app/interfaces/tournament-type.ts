import { GameType } from './game-type';
import { UserType } from './user-type';
import { ParticipantType } from './participant-type';
import {FixtureType} from "./fixture-type";

export interface TournamentType {
    tournamentId?: number | string;
    attended: boolean;
    date: string;
    time: string;
    participantCount: number | string;
    currentParticipants?: number | string;
    status: number | string;
    referenceCode?: string;
    tournamentType: string;
    price: {
        amount: number | string,
        currency: string
    },
    game: GameType,
    holder: UserType,
    participants?: ParticipantType[],
    fixture?: FixtureType;
}
