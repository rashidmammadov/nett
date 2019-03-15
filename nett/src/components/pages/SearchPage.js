import React, {Component} from 'react';
import {View} from 'react-native';
import TournamentsList from '../TournamentsList.js';

export default class SearchPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            tournaments: [{
                tournamentId: 2,
                attended: true,
                holderImage: {uri: 'https://beta.ozelden.com/img/logo/logo-min.png'},
                holder: 'PlayStation 24',
                location: 'Bornova/Izmir',
                gameName: 'fifa19',
                gameImage: {uri: 'https://i.cnnturk.com/ps/cnnturk/75/650x325/5b6f078661361f2a14f6eea9.jpg'},
                date: '29/03',
                price: '15 ₺',
                participantCount: 24,
                currentParticipants: 20

            }, {
                tournamentId: 1,
                attended: false,
                holderImage: {uri: 'https://beta.ozelden.com/img/logo/logo-min.png'},
                holder: 'Keko Bilişim',
                location: 'Bornova/Izmir',
                gameName: 'PUBG',
                date: '01/04',
                price: '15 ₺',
                participantCount: 32,
                currentParticipants: 25,
                gameImage: {uri: 'https://steamcdn-a.akamaihd.net/steam/apps/578080/header.jpg?t=1545084399'}
            },]
        };
    }

    render() {
        return (
            <View>
                <TournamentsList tournaments={this.state.tournaments}/>
            </View>
        );
    }
}