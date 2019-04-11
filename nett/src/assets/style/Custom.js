import {StyleSheet} from 'react-native';

export const style = StyleSheet.create({
    flex: {
        flex: 1
    },
    fontFamily: {
        fontFamily: 'GoogleSans-Regular'
    },
    primaryTextColor: {
        color: '#f8f8f8'
    },
    secondaryTextColor: {
        color: '#303030'
    },
    primaryBGColor: {
        backgroundColor: '#000000'
    },
    secondaryBGColor: {
        backgroundColor: '#303030'
    },
    smallFont: {
        fontSize: 14
    },
    boldFont: {
        fontWeight: 'bold'
    },
    alignCenter: {
        alignItems: 'center'
    },
    alignColumn: {
        flexDirection: 'column'
    },

    knockOutContainer: {
        paddingLeft: 0,
        paddingRight: 16
    },
    knockOutInnerContainer: {
        flex: 1,
        marginLeft: 0,
        flexDirection: 'row',
        justifyContent: 'space-between',
        alignItems: 'center'
    },
    knockOutBadge: {
        width: 72,
        backgroundColor: '#f8f8f8',
        marginLeft: 8,
        marginRight: 8
    },

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