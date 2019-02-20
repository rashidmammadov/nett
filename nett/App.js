/**
 * Sample React Native App
 * https://github.com/facebook/react-native
 *
 * @format
 * @flow
 * @lint-ignore-every XPLATJSCOPYRIGHT1
 */

import React, {Component} from 'react';
import {Platform, StyleSheet, Text, View} from 'react-native';
import {Actions, Scene, Router} from 'react-native-router-flux';
import DashboardPage from './src/components/pages/DashboardPage.js';
import SignPage from './src/components/pages/SignPage.js';
import StartPage from './src/components/pages/StartPage.js';

type Props = {};
export default class App extends Component<Props> {
    render() {
        const scenes = Actions.create(
            <Scene key="root">
                <Scene key="DashboardPage" component={DashboardPage} title="Dashboard" hideNavBar={true}/>
                <Scene key="SignPage" component={SignPage}/>
                <Scene key="StartPage" component={StartPage} initial={true} hideNavBar={true}/>
            </Scene>
        );

        return <Router scenes={scenes}/>
    }
}
