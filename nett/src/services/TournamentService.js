import {apiUrl, requestHeader} from './ConfigService';
import {STATUS} from "./Constants";

export const search = (data) => {
    const URL = apiUrl + 'tournaments?' + STATUS + '=' + encodeURIComponent(data.status);
    return fetch(URL, {
        method: 'GET',
        headers: requestHeader()
    }).then((res) => res.json());
};
