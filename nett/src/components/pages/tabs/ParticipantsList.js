import React, {Component} from 'react';
import {ScrollView, View} from 'react-native';
import {List, ListItem, Thumbnail, Text, Left, Body, Right} from 'native-base';
import {style} from '../../../assets/style/Custom.js';

export default class ParticipantsList extends Component {

    constructor(props) {
        super(props);
        this.state = {};
    }

    render() {
        let participants = this.props.participants && this.props.participants.map((participant, i) => {
            return <ListItem avatar key={i}>
                <Left>
                    <Thumbnail style={style.imageBGColor} small source={{uri: participant.picture}}/>
                </Left>
                <Body>
                <Text style={[style.fontFamily, style.primaryTextColor]}>{participant.username}</Text>
                <Text note style={style.fontFamily} numberOfLines={1}>{participant.name} {participant.surname}</Text>
                </Body>
                <Right>
                    <Text note
                          style={style.fontFamily}>{participant.tournamentRanking ? '#' + participant.tournamentRanking : '-'}</Text>
                    <Text note
                          style={style.fontFamily}>{participant.referenceCode ? participant.referenceCode : ''}</Text>
                </Right>
            </ListItem>
        });

        if (this.props.participants && this.props.participants.length > 0) {
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