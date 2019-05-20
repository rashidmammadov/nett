import {apiUrl, requestHeader} from './ConfigService';

export const setMatchScore = (data) => {
    const URL = apiUrl + 'fixture';
    return fetch(URL, {
        method: 'POST',
        headers: requestHeader(),
        body: JSON.stringify(data)
    }).then((res) => res.json());
};