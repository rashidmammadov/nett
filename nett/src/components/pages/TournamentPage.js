import React, {Component} from 'react';
import {BackHandler, ImageBackground, View, TouchableOpacity} from 'react-native';
import {Tab, Tabs, Left, Body, Title, Header} from "native-base";
import {Actions} from "react-native-router-flux";
import {getTournament} from "../../services/TournamentService";
import {SUCCESS} from "../../services/Constants";
import {errorToast, warningToast} from "../../services/ToastService";
import LoadingDialog from "../LoadingDialog";
import Icon from 'react-native-vector-icons/Feather';
import {style} from "../../assets/style/Custom";
import TournamentDetail from './tabs/TournamentDetail.js';
import ParticipantsList from './tabs/ParticipantsList.js';
import KnockOutFixture from './tabs/KnockOutFixture.js';
import {googleTrack} from "../../services/GoogleAnalytics";

export default class TournamentPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: true,
            tournament: {}
        };

        this.handleBackButtonClick = this.handleBackButtonClick.bind(this);
    }

    componentDidMount() {
        let tournamentId = this.props.tournamentId;
        googleTrack('Tournament Page', 'send request to get tournament data', 'tournament: '.tournamentId);
        getTournament(tournamentId)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    this.setState({tournament: res.data});
                    googleTrack('Tournament Page', 'success response of tournament data', res.data);
                } else {
                    warningToast(res.message);
                    googleTrack('Tournament Page', 'warning response of tournament data', res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
                googleTrack('Tournament Page', 'error response of tournament data', error.message);
            });

        BackHandler.addEventListener('hardwareBackPress', this.handleBackButtonClick);
    }

    componentWillUnmount() {
        BackHandler.removeEventListener('hardwareBackPress', this.handleBackButtonClick);
    }

    handleBackButtonClick() {
        Actions.pop();
        return true;
    }

    render() {
        return (
            <View transparent style={style.flex}>
                <LoadingDialog loading={this.state.loading}/>
                <Header style={style.secondaryBGColor}>
                    <Left>
                        <TouchableOpacity transparent onPress={() => this.handleBackButtonClick()}>
                            <Icon name='arrow-left' color={'#f8f8f8'} size={24}/>
                        </TouchableOpacity>
                    </Left>
                    <Body>
                    <Title style={[style.primaryTextColor, style.fontFamily]}>{this.props.title}</Title>
                    </Body>
                </Header>
                <ImageBackground
                    source={{uri: this.state.tournament.game && this.state.tournament.game.image}}
                    style={{height: 96, width: null, resizeMode: 'cover'}}>
                </ImageBackground>
                <Tabs>
                    <Tab heading="Genel" tabStyle={style.secondaryBGColor}
                         style={style.primaryBGColor}
                         textStyle={style.tabTextStyle}
                         activeTabStyle={style.secondaryBGColor}
                         activeTextStyle={style.tabActiveTextStyle}>
                        <TournamentDetail detail={this.state.tournament}/>
                    </Tab>
                    <Tab heading="Katılımcılar" tabStyle={style.secondaryBGColor}
                         style={style.primaryBGColor}
                         textStyle={style.tabTextStyle}
                         activeTabStyle={style.secondaryBGColor}
                         activeTextStyle={style.tabActiveTextStyle}>
                        <ParticipantsList participants={this.state.tournament.participants}/>
                    </Tab>
                    <Tab heading="Fikstür" tabStyle={style.secondaryBGColor}
                         style={style.primaryBGColor}
                         textStyle={style.tabTextStyle}
                         activeTabStyle={style.secondaryBGColor}
                         activeTextStyle={style.tabActiveTextStyle}>
                        <KnockOutFixture fixture={this.state.tournament.fixture}/>
                    </Tab>
                </Tabs>
            </View>
        );
    }
}