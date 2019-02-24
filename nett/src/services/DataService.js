export const regions = () => {
    const URL = 'http://api.ozelden.com/api/v1/data?regions=true';
    return fetch(URL, {
        method: 'GET',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
        }
    }).then((res) => res.json());
}