import {StyleSheet} from 'react-native';

export const style = StyleSheet.create({
    columnCenter: {
        flex: 1,
        flexDirection: 'column',
        alignItems: 'center',
        justifyContent: 'center'
    },
    footer: {
        backgroundColor: '#000'
    },
    footerTab: {
        backgroundColor: '#303030',
        color: '#f8f8f8'
    },
    footerIcon: {
        color: '#f8f8f8'
    },
    footerActiveTab: {
        backgroundColor: '#f8f8f8',
        color: '#303030'
    },
    footerActiveIcon: {
        color: '#303003'
    }
});