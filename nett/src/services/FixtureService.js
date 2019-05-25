import {apiUrl, requestHeader} from './ConfigService';

export const setMatchScore = (data) => {
    const URL = apiUrl + 'fixture';
    return fetch(URL, {
        method: 'PUT',
        headers: requestHeader(),
        body: JSON.stringify(data)
    }).then((res) => res.json());
};