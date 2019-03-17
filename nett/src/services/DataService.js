import {requestHeader} from "./ConfigService";

export const regions = () => {
    const URL = 'http://api.ozelden.com/api/v1/data?regions=true';
    return fetch(URL, {
        method: 'GET',
        headers: requestHeader()
    }).then((res) => res.json());
}