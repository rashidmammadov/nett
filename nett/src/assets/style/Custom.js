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
    customBGColor: {
        backgroundColor: '#7F00FF'
    },
    imageBGColor: {
        backgroundColor: '#d3d3d3'
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
    padding16: {
        padding: 16
    },
    height64: {
        height: 64
    },
    innerMarginTop: {
        marginTop: -64
    },
    marginTop16: {
        marginTop: 16
    },
    marginBottom16: {
        marginBottom: 16
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

    tabTextStyle: {
        color: '#a3a3a3',
        fontFamily: 'GoogleSans-Regular',
        fontWeight: 'normal'
    },
    tabActiveTextStyle: {
        color: '#f8f8f8',
        fontFamily: 'GoogleSans-Regular',
        fontWeight: 'normal'
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