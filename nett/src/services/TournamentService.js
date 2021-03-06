import {apiUrl, requestHeader} from './ConfigService';
import {STATUS} from "./Constants";

export const add = (data) => {
    const URL = apiUrl + 'tournaments';
    return fetch(URL, {
        method: 'POST',
        headers: requestHeader(),
        body: JSON.stringify(data)
    }).then((res) => res.json());
};

export const search = (data) => {
    const URL = apiUrl + 'tournaments?' + STATUS + '=' + encodeURIComponent(data.status);
    return fetch(URL, {
        method: 'GET',
        headers: requestHeader()
    }).then((res) => res.json());
};

export const getTournament = (tournamentId) => {
    const URL = apiUrl + 'tournaments/' + tournamentId;
    return fetch(URL, {
        method: 'GET',
        headers: requestHeader()
    }).then((res) => res.json());
};

export const getMyTournaments = (data) => {
    const URL = apiUrl + 'myTournaments?' + STATUS + '=' + encodeURIComponent(data.status);
    return fetch(URL, {
        method: 'GET',
        headers: requestHeader()
    }).then((res) => res.json());
};
