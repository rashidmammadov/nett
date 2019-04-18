/**
 * Sample React Native App
 * https://github.com/facebook/react-native
 *
 * @format
 * @flow
 * @lint-ignore-every XPLATJSCOPYRIGHT1
 */

import React, {Component} from 'react';
import {AsyncStorage} from 'react-native';
import {Actions, Scene, Router} from 'react-native-router-flux';
import OneSignal from 'react-native-onesignal';
import AppPage from './src/components/pages/AppPage.js';
import BudgetPage from './src/components/pages/BudgetPage.js';
import DashboardPage from './src/components/pages/DashboardPage.js';
import LoginPage from './src/components/pages/LoginPage.js';
import MyTournamentsPage from './src/components/pages/MyTournamentsPage.js';
import RegisterPage from './src/components/pages/RegisterPage.js';
import SearchPage from './src/components/pages/SearchPage.js';
import SetTournamentPage from './src/components/pages/SetTournamentPage.js';
import StartPage from './src/components/pages/StartPage.js';
import TournamentPage from './src/components/pages/TournamentPage.js';
import {ONESIGNAL_DEVICE_ID, ONESIGNAL_APPID} from "./src/services/Constants";

//type Props = {};
export default class App extends Component {

    constructor(props) {
        super(props);
        OneSignal.init(ONESIGNAL_APPID);

        OneSignal.addEventListener('received', this.onReceived);
        OneSignal.addEventListener('opened', this.onOpened);
        OneSignal.addEventListener('ids', this.onIds);

        OneSignal.getPermissionSubscriptionState((status) => {
            console.log('device id', status.userId);
            AsyncStorage.setItem(ONESIGNAL_DEVICE_ID, status.userId);
        });
    }

    componentWillUnmount() {
        OneSignal.removeEventListener('received', this.onReceived);
        OneSignal.removeEventListener('opened', this.onOpened);
        OneSignal.removeEventListener('ids', this.onIds);
    }

    onReceived(notification) {
        console.log("Notification received: ", notification);
    }

    onOpened(openResult) {
        console.log('Message: ', openResult.notification.payload.body);
        console.log('Data: ', openResult.notification.payload.additionalData);
        console.log('isActive: ', openResult.notification.isAppInFocus);
        console.log('openResult: ', openResult);
    }

    onIds(device) {
        console.log('Device info: ', device);
    }

    render() {
        const scenes = Actions.create(
            <Scene key="root">
                <Scene key="AppPage" component={AppPage} title="nett" hideNavBar={true}/>
                <Scene key="BudgetPage" component={BudgetPage} hideNavBar={true}/>
                <Scene key="DashboardPage" component={DashboardPage} hideNavBar={true}/>
                <Scene key="LoginPage" component={LoginPage} hideNavBar={true}/>
                <Scene key="MyTournamentsPage" component={MyTournamentsPage} hideNavBar={true}/>
                <Scene key="RegisterPage" component={RegisterPage} hideNavBar={true}/>
                <Scene key="SearchPage" component={SearchPage} hideNavBar={true}/>
                <Scene key="SetTournamentPage" component={SetTournamentPage} hideNavBar={true}/>
                <Scene key="StartPage" component={StartPage} initial={true} hideNavBar={true}/>
                <Scene key="TournamentPage" component={TournamentPage} title="Turnuva Detayı" hideNavBar={true}
                       back={true}/>
            </Scene>
        );

        return <Router scenes={scenes}/>
    }
}
