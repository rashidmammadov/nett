import {Toast} from "native-base";

export const successToast = (message) => {
    Toast.show({
        text: message,
        buttonText: 'tamam',
        type: 'success'
    });
};

export const warningToast = (message) => {
    Toast.show({
        text: message,
        buttonText: 'tamam',
        type: 'warning'
    });
};

export const errorToast = (message) => {
    Toast.show({
        text: message,
        buttonText: 'tamam',
        type: 'danger'
    });
};