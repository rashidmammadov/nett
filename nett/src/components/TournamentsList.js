import React, { Component } from 'react';
import {View, FlatList, Text} from 'react-native';
import TournamentCard from './TournamentCard.js';
import {NO_RESULT} from "../services/Constants";

export default class TournamentsList extends Component {

    render() {
        let noSearchResult = () => {
            if (!this.props.loading && this.props.tournaments.length === 0) {
                return <View style={{flex: 1, justifyContent: 'center', alignItems: 'center'}}>
                        <Text style={{fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>
                            {NO_RESULT}
                        </Text>
                    </View>
            }
        };
        return (
            <View style={{flex: 1}}>
                {noSearchResult()}
                <FlatList
                    data={this.props.tournaments}
                    renderItem={({item}) => <TournamentCard tournament={item}/>}
                    keyExtractor={(item, index) => index.toString()}
                />
            </View>
        );
    }
}