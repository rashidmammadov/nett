import React, {Component} from 'react';
import {ImageBackground, View} from 'react-native';
import {Card, CardItem, Thumbnail, Text, Left, Body, Right} from 'native-base';
import AttendTournament from './AttendTournament';
import Icon from 'react-native-vector-icons/Feather';

export default class TournamentCard extends Component {

    constructor(props) {
        super(props);
        this.state = {
            data: this.props.tournament
        };
    }

    render() {
        return (
            <Card style={{backgroundColor: '#303030', borderColor: '#303030'}}>
                <CardItem style={{backgroundColor: 'transparent'}}>
                    <Left>
                        <Thumbnail small source={this.state.data.holderImage} style={{backgroundColor: '#d3d3d3'}} />
                        <Body>
                              <Text style={{fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>
                                  {this.state.data.holder}
                              </Text>
                              <Text note style={{fontFamily: 'GoogleSans-Regular'}}>{this.state.data.location}</Text>
                        </Body>
                    </Left>
                    <Right>
                        <AttendTournament data={this.props.tournament}/>
                    </Right>
                </CardItem>
                <CardItem cardBody>
                    <ImageBackground source={this.state.data.gameImage}
                           style={{height: 200, width: null, flex: 1}}>
                    </ImageBackground>
                </CardItem>
                <CardItem style={{backgroundColor: 'transparent'}}>
                    <Left>
                        <Icon name='tag' size={24} color={'#f8f8f8'}/>
                        <Text style={{fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>{this.state.data.gameName}</Text>
                    </Left>
                    <View style={{flex: 1, flexDirection: 'row', justifyContent: 'space-between'}}>
                        <View style={{justifyContent: 'center', alignItems: 'center'}}>
                            <Text note style={{fontFamily: 'GoogleSans-Regular'}}>Tarih</Text>
                            <Text style={{fontSize: 16, fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>{this.state.data.date}</Text>
                        </View>
                        <View style={{justifyContent: 'center', alignItems: 'center'}}>
                            <Text note style={{fontFamily: 'GoogleSans-Regular'}}>Ücret</Text>
                            <Text style={{fontSize: 16, fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>{this.state.data.price}</Text>
                        </View>
                        <View style={{justifyContent: 'center', alignItems: 'center'}}>
                            <Text note style={{fontFamily: 'GoogleSans-Regular'}}>Katılımcı</Text>
                            <Text style={{fontSize: 16, fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>{this.state.data.participantCount}</Text>
                        </View>
                    </View>
                </CardItem>
            </Card>
        );
    }
}