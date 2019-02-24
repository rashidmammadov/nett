export const signIn = (user) => {
    const URL = 'http://api.ozelden.com/api/v1/login';
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
    const URL = 'http://api.ozelden.com/api/v1/register';
    return fetch(URL, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(user)
    }).then((res) => res.json());
}