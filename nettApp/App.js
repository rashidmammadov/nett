/**
 * Sample React Native App
 * https://github.com/facebook/react-native
 *
 * @format
 * @flow
 * @lint-ignore-every XPLATJSCOPYRIGHT1
 */

import React, {Component} from 'react';
import {Platform, StyleSheet, Text, View, Image} from 'react-native';
import TournamentsList from './js/components/TournamentsList.js';

type Props = {};

export default class App extends Component<Props> {

    constructor(props) {
        super(props);
        this.state = {
            tournaments: [
                {key: '1', image: {uri: 'https://beta.ozelden.com/img/logo/logo-min.png'}, holder: 'Play 24'},
                {key: '2', image: {uri: 'https://beta.ozelden.com/img/logo/logo-min.png'}, holder: 'Küçükpark PlayStation'}
            ]
        };
    }

    render() {
        return (
            <TournamentsList tournaments={this.state.tournaments}/>
        );
    }
}
