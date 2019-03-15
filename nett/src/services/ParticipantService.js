import {apiUrl} from './ConfigService';

export const attend = (data) => {
    const URL = apiUrl + 'participant';
    return fetch(URL, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    }).then((res) => res.json());
};

export const leave = (data) => {
    const URL = apiUrl + 'participant';
    return fetch(URL, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    }).then((res) => res.json());
};
