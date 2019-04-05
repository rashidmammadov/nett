import React, {Component} from 'react';
import {ImageBackground, View, TouchableOpacity} from 'react-native';
import {Tab, Tabs, Root, Left, Body, Title, Header} from "native-base";
import {Actions} from "react-native-router-flux";
import {getTournament} from "../../services/TournamentService";
import {SUCCESS} from "../../services/Constants";
import {errorToast, warningToast} from "../../services/ToastService";
import LoadingDialog from "../LoadingDialog";
import Icon from 'react-native-vector-icons/Feather';
import TournamentDetail from './tabs/TournamentDetail.js';
import ParticipantsList from './tabs/ParticipantsList.js';
import KnockOutFixture from './tabs/KnockOutFixture.js';

export default class TournamentPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: true,
            tournament: null
        }
    }

    componentDidMount() {
        let tournamentId = this.props.tournamentId;
        getTournament(tournamentId)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    this.setState({tournament: res.data});
                } else {
                    warningToast(res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
            });
    }

    render() {
        return (
            <View transparent style={{flex: 1}}>
                <LoadingDialog loading={this.state.loading}/>
                <Header style={{backgroundColor: '#303030'}}>
                    <Left>
                        <TouchableOpacity transparent onPress={() => Actions.pop()}>
                            <Icon name='arrow-left' color={'#f8f8f8'} size={24}/>
                        </TouchableOpacity>
                    </Left>
                    <Body>
                    <Title style={{
                        color: '#f8f8f8',
                        fontSize: 20,
                        fontFamily: 'GoogleSans-Regular'
                    }}>{this.props.title}</Title>
                    </Body>
                </Header>
                <ImageBackground
                    source={{uri: 'https://s3.eu-central-1.amazonaws.com/images.gamesatis.com/products/images/000/023/869/big/fifa-19.jpg'}}
                    style={{height: 96, width: null, resizeMode: 'cover'}}>
                </ImageBackground>
                <Tabs>
                    <Tab heading="Genel" tabStyle={{backgroundColor: '#303030'}}
                         style={{backgroundColor: '#000'}}
                         textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                         activeTabStyle={{backgroundColor: '#303030'}}
                         activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        <TournamentDetail/>
                    </Tab>
                    <Tab heading="Katılımcılar" tabStyle={{backgroundColor: '#303030'}}
                         style={{backgroundColor: '#000'}}
                         textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                         activeTabStyle={{backgroundColor: '#303030'}}
                         activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        <ParticipantsList/>
                    </Tab>
                    <Tab heading="Fikstür" tabStyle={{backgroundColor: '#303030'}}
                         style={{backgroundColor: '#000'}}
                         textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                         activeTabStyle={{backgroundColor: '#303030'}}
                         activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        <KnockOutFixture/>
                    </Tab>
                </Tabs>
            </View>
        );
    }
}