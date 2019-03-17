import {apiUrl, requestHeader} from './ConfigService';

export const attend = (data) => {
    const URL = apiUrl + 'participant';
    return fetch(URL, {
        method: 'POST',
        headers: requestHeader(),
        body: JSON.stringify(data)
    }).then((res) => res.json());
};

export const leave = (data) => {
    const URL = apiUrl + 'participant';
    return fetch(URL, {
        method: 'DELETE',
        headers: requestHeader(),
        body: JSON.stringify(data)
    }).then((res) => res.json());
};
