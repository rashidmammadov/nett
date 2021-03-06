import {apiUrl, requestHeader} from './ConfigService';

export const activate = (user) => {
    const URL = apiUrl + 'activate';
    return fetch(URL, {
        method: 'PUT',
        headers: requestHeader(),
        body: JSON.stringify(user)
    }).then((res) => res.json());
};

export const signIn = (user) => {
    const URL = apiUrl + 'login';
    return fetch(URL, {
        method: 'POST',
        headers: requestHeader(),
        body: JSON.stringify(user)
    }).then((res) => res.json());
};

export const signUp = (user) => {
    const URL = apiUrl + 'register';
    return fetch(URL, {
        method: 'POST',
        headers: requestHeader(),
        body: JSON.stringify(user)
    }).then((res) => res.json());
};

export const refreshUser = () => {
    const URL = apiUrl + 'refreshUser';
    return fetch(URL, {
        method: 'GET',
        headers: requestHeader()
    }).then((res) => res.json());
};