import {apiUrl} from './ConfigService';

export const signIn = (user) => {
    const URL = apiUrl + 'login';
    return fetch(URL, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(user)
    }).then((res) => res.json());
}

export const signUp = (user) => {
    const URL = apiUrl + 'register';
    return fetch(URL, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(user)
    }).then((res) => res.json());
}