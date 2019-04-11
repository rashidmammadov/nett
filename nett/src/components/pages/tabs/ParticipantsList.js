import React, {Component} from 'react';
import {ScrollView, View} from 'react-native';
import {List, ListItem, Thumbnail, Text, Left, Body, Right} from 'native-base';
import {style} from '../../../assets/style/Custom.js';

export default class ParticipantsList extends Component {

    constructor(props) {
        super(props);
        this.state = {
            participants: this.props.participants
        };
    }

    render() {
        let participants = this.state.participants && this.state.participants.map((participant, i) => {
            return <ListItem avatar key={i}>
                <Left>
                    <Thumbnail small source={{uri: participant.picture}}/>
                </Left>
                <Body>
                <Text style={[style.fontFamily, style.primaryTextColor]}>{participant.username}</Text>
                <Text note style={style.fontFamily} numberOfLines={1}>{participant.name} {participant.surname}</Text>
                </Body>
                <Right>
                    <Text note
                          style={style.fontFamily}>{participant.tournamentRanking ? '#'.tournamentRanking : '-'}</Text>
                </Right>
            </ListItem>
        });

        if (this.state.participants) {
            return (
                <ScrollView>
                    <List>
                        {participants}
                    </List>
                </ScrollView>
            )
        } else {
            return <View style={style.columnCenter}>
                <Text style={[style.fontFamily, style.primaryTextColor]}>Katılımcı bilgileri bulunamadı</Text>
            </View>
        }

    }
}