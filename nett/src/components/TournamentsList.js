import React, { Component } from 'react';
import { View, FlatList } from 'react-native';
import TournamentCard from './TournamentCard.js';

export default class TournamentsList extends Component {

    render() {
        return (
            <View>
                <FlatList
                    data={this.props.tournaments}
                    renderItem = {({item}) => <TournamentCard tournament={item}/>}
                />
            </View>
        );
    }
}