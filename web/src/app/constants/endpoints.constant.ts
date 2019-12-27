import { environment } from '../../environments/environment';

const PREFIX = environment.apiPrefix;

export const ENDPOINTS = {

    ACTIVATE: () => `${PREFIX}activate`,

    FINANCE: (page: number = null, itemPerPage: number = null) => page && itemPerPage ?
        `${PREFIX}finance?page=${page}&itemPerPage=${itemPerPage}` : `${PREFIX}finance`,

    FIXTURE: () => `${PREFIX}fixture`,

    GAMES: (id:number = null) => !!id ? `${PREFIX}games/${id}` : `${PREFIX}games`,

    LOGIN: () => `${PREFIX}login`,

    LOGOUT: () => `${PREFIX}logout`,

    PARTICIPANTS: (tournamentId?: number) => !!tournamentId ? `${PREFIX}participants?tournamentId=${tournamentId}` : `${PREFIX}participants`,

    REFRESH_USER: () => `${PREFIX}refreshUser`,

    REGISTER: () => `${PREFIX}register`,

    REGIONS: (regions: boolean, taxOffices: boolean) => `${PREFIX}data?regions=${regions}&taxOffices=${taxOffices}`,

    REPORTS: (reportType: string) => `${PREFIX}reports?reportType=${reportType}`,

    RESET_PASSWORD: () => `${PREFIX}resetPassword`,

    MY_TOURNAMENTS: (status: number | string = 0) => {
        let queryParams: string = '';
        status >= 0 && (queryParams = `?status=${status}`);
        return `${PREFIX}myTournaments${queryParams}`;
    },

    TOURNAMENTS: (tournamentId?: number | string, status: number | string = 0) => {
        let queryParams: string = '';
        status >= 0 && (queryParams = `?status=${status}`);
        return PREFIX + (!!tournamentId ? `tournaments/${tournamentId}` : 'tournaments') + queryParams;
    },

    UPDATE_PASSWORD: () => `${PREFIX}updatePassword`,

    UPDATE_SETTINGS: () => `${PREFIX}updateSettings`,

};
