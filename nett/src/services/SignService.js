export const signIn = (user) => {
    let email = user.email;
    let password = user.password;
    const URL = 'http://api.ozelden.com/api/v1/login';
    return fetch(URL, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            email: email,
            password: password
        })
    }).then((res) => res.json());
}