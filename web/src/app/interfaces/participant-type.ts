export interface ParticipantType {
    name: string;
    participantId: number | string;
    paymentAmount?: number | string;
    paymentType?: string;
    picture?: string;
    referenceCode?: string;
    surname: string;
    tournamentRanking?: number | string;
    username: string;
}
