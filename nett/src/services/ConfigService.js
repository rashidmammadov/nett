import {AsyncStorage} from "react-native";

export const apiUrl = 'http://nett.ozelden.com/api/v1/';

export let user = null;

AsyncStorage.getItem('user').then((value) => {
    user = value ? JSON.parse(value) : null;
});

export let setUser = (data) => {
    user = data;
};

export let requestHeader = () => {
    return {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + (user ? user.remember_token : null)
    }
};
