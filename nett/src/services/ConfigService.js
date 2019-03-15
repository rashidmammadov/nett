import {AsyncStorage} from "react-native";

export const apiUrl = 'http://nett.ozelden.com/api/v1/';

export let token = null;
AsyncStorage.getItem('user').then((value) => {
    token = value ? JSON.parse(value).remember_token : null;
});