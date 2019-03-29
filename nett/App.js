/**
 * Sample React Native App
 * https://github.com/facebook/react-native
 *
 * @format
 * @flow
 * @lint-ignore-every XPLATJSCOPYRIGHT1
 */

import React, {Component} from 'react';
import {Actions, Scene, Router} from 'react-native-router-flux';
import AppPage from './src/components/pages/AppPage.js';
import BudgetPage from './src/components/pages/BudgetPage.js';
import DashboardPage from './src/components/pages/DashboardPage.js';
import LoginPage from './src/components/pages/LoginPage.js';
import MyTournamentsPage from './src/components/pages/MyTournamentsPage.js';
import RegisterPage from './src/components/pages/RegisterPage.js';
import SearchPage from './src/components/pages/SearchPage.js';
import SetTournamentPage from './src/components/pages/SetTournamentPage.js';
import StartPage from './src/components/pages/StartPage.js';

type Props = {};
export default class App extends Component<Props> {
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
            </Scene>
        );

        return <Router scenes={scenes}/>
    }
}
