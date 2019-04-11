import React, {Component} from 'react';
import {ScrollView} from 'react-native';
import {List, ListItem, Text, Left, Body, Right} from "native-base";
import {style} from '../../../assets/style/Custom.js';
import Icon from 'react-native-vector-icons/Feather';
import {GROUP, KNOCK_OUT, RANKING} from "../../../services/Constants";

const typeMap = {
    knock_out: KNOCK_OUT,
    group: GROUP,
    ranking: RANKING
};

export default class TournamentDetail extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: true,
            detail: this.props.detail
        }
    }

    render() {
        return (
            <ScrollView>
                <List>
                    <ListItem avatar>
                        <Left>
                            <Icon name="hash" size={24} color={'#d3d3d3'}/>
                        </Left>
                        <Body>
                        <Text style={[style.fontFamily, style.primaryTextColor]}>Oyun</Text>
                        </Body>
                        <Right style={style.flex}>
                            <Text style={[style.fontFamily, style.primaryTextColor]}>
                                {this.state.detail.game ? this.state.detail.game.name : '-'}
                            </Text>
                        </Right>
                    </ListItem>
                    <ListItem avatar>
                        <Left>
                            <Icon name="list" size={24} color={'#d3d3d3'}/>
                        </Left>
                        <Body>
                        <Text style={[style.fontFamily, style.primaryTextColor]}>Turnuva Tipi</Text>
                        </Body>
                        <Right style={style.flex}>
                            <Text style={[style.fontFamily, style.primaryTextColor]}>
                                {typeMap[this.state.detail.tournamentType]}
                            </Text>
                        </Right>
                    </ListItem>
                    <ListItem avatar>
                        <Left>
                            <Icon name="clock" size={24} color={'#d3d3d3'}/>
                        </Left>
                        <Body>
                        <Text style={[style.fontFamily, style.primaryTextColor]}>Başlangıç Tarih</Text>
                        </Body>
                        <Right style={style.flex}>
                            <Text style={[style.fontFamily, style.primaryTextColor]}>
                                {this.state.detail.time} {this.state.detail.date}
                            </Text>
                        </Right>
                    </ListItem>
                    <ListItem avatar>
                        <Left>
                            <Icon name="users" size={24} color={'#d3d3d3'}/>
                        </Left>
                        <Body>
                        <Text style={[style.fontFamily, style.primaryTextColor]}>Katılımcı Sayısı</Text>
                        </Body>
                        <Right style={style.flex}>
                            <Text style={[style.fontFamily, style.primaryTextColor]}>
                                {this.state.detail.participantCount}
                            </Text>
                        </Right>
                    </ListItem>
                    <ListItem avatar>
                        <Left>
                            <Icon name="dollar-sign" size={24} color={'#d3d3d3'}/>
                        </Left>
                        <Body>
                        <Text style={[style.fontFamily, style.primaryTextColor]}>Katılım Ücreti</Text>
                        </Body>
                        <Right style={style.flex}>
                            <Text style={[style.fontFamily, style.primaryTextColor]}>
                                {this.state.detail.price ? this.state.detail.price.amount + ' ' + this.state.detail.price.currency : '-'}
                            </Text>
                        </Right>
                    </ListItem>
                </List>
            </ScrollView>
        );
    }
}