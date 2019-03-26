import React, {Component} from 'react';
import {View, ScrollView} from 'react-native';
import {ListItem, Text, Left, Body, DatePicker, Picker, Right, Button} from 'native-base';
import DateTimePicker from 'react-native-modal-datetime-picker';
import {games} from "../../services/DataService";
import {add} from "../../services/TournamentService";
import Icon from 'react-native-vector-icons/Feather';
import {SUCCESS} from "../../services/Constants";
import {errorToast, successToast, warningToast} from "../../services/ToastService";
import LoadingDialog from "../LoadingDialog";

export default class SetTournamentPage extends Component {

    constructor(props) {
        super(props);
        let participantList = [];
        for (let i=16; i<=32; i++) {
            participantList.push(i.toString());
        }
        this.state = {
            loading: true,
            games: [],
            gameTypes: [],
            participantList: participantList,
            selectedParticipant: participantList[0],
            selectedDate: new Date(),
            selectedTime: new Date(),
            income: (Number(participantList[0]) * (5 + Number(participantList[0]) / 11)).toFixed(2)
        };

        this.getGamesList();
        this.setDate = this.setDate.bind(this);
    }

    getGamesList() {
        games()
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    let games = res.data;
                    this.setState({
                        games: games,
                        selectedGame: games[0],
                        gameTypes: games[0].playingType,
                        selectedType: games[0].playingType[0]
                    });
                } else {
                    warningToast(res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
            });
    }

    onGameChange(value) {
        this.setState({
            selectedGame: value,
            gameTypes: value.playingType,
            selectedType: this.state.gameTypes[0]
        })
    }

    onTypeChange(value) {
        this.setState({ selectedType: value });
    }

    onParticipantChange(value) {
        this.setState({
            selectedParticipant: value,
            income: (Number(value) * (5 + Number(value) / 11)).toFixed(2)
        });
    }

    setDate(newDate) {
        this.setState({ selectedDate: newDate });
    }

    setTournament() {
        let data = this.$$prepareData();
        this.setState({loading: true});
        add(data)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    successToast(res.message)
                } else {
                    warningToast(res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
            });
    }

    $$prepareData() {
        let date = new Date(
            this.state.selectedDate.getFullYear(),
            this.state.selectedDate.getMonth(),
            this.state.selectedDate.getDate(),
            this.state.selectedTime.getHours(),
            this.state.selectedTime.getMinutes()
        );

        return {
            gameId: this.state.selectedGame.gameId,
            tournamentType: this.state.selectedType,
            days: 1,
            participantCount: this.state.selectedParticipant,
            startDate: date.getTime()
        };
    }

    render() {
        const currentDate = new Date();
        const minDate = new Date(currentDate.getFullYear(), currentDate.getMonth(), currentDate.getDate() + 7);
        const maxDate = new Date(currentDate.getFullYear() + 1, currentDate.getMonth(), currentDate.getDate());

        let gamePickers = this.state.games.map( (game) => {
            return <Picker.Item key={game.gameId} value={game} label={game.gameName} />
        });

        let typePickers = this.state.gameTypes.map( (type, i) => {
            return <Picker.Item key={i} value={type} label={type} />
        });

        let participantPickers = this.state.participantList.map((count, i) => {
            return <Picker.Item key={i} value={count} label={count} />
        });

        let showDefaultDate = () => {
          return minDate.getDate() + '/' + (minDate.getMonth() + 1) + '/' + minDate.getFullYear()
        };

        return (
            <ScrollView>
                <LoadingDialog loading={this.state.loading}/>
                <View style={{height: 64, backgroundColor: '#7F00FF'}}>
                </View>
                <View style={{padding: 16, marginTop: -64}}>
                    <Text style={{color: '#f8f8f8', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Turnuva Düzenle</Text>
                    <View style={{marginBottom: 16, backgroundColor: '#303030'}}>
                        <ListItem icon>
                            <Left>
                                <Icon name="hash" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={{borderColor: '#303030'}}>
                                <Text  style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}>Oyun</Text>
                            </Body>
                                <Picker mode="dropdown" placeholder="Oyun"
                                    style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}
                                    selectedValue={this.state.selectedGame}
                                    onValueChange={this.onGameChange.bind(this)}>
                                    {gamePickers}
                                </Picker>
                        </ListItem>
                        <ListItem icon>
                            <Left>
                                <Icon name="list" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={{borderColor: '#303030'}}>
                                <Text  style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}>Turnuva Tipi</Text>
                            </Body>
                            <Picker
                                mode="dropdown"
                                placeholder="Oyun"
                                style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}
                                selectedValue={this.state.selectedType}
                                onValueChange={this.onTypeChange.bind(this)}
                                >
                                {typePickers}
                            </Picker>
                        </ListItem>
                        <ListItem icon>
                            <Left>
                                <Icon name="users" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={{borderColor: '#303030'}}>
                                <Text  style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}>Katılımcı Sayı</Text>
                            </Body>
                            <Picker
                                mode="dropdown"
                                placeholder="Oyun"
                                style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}
                                selectedValue={this.state.selectedParticipant}
                                onValueChange={this.onParticipantChange.bind(this)}
                                >
                                {participantPickers}
                            </Picker>
                        </ListItem>
                        <ListItem icon>
                            <Left>
                                <Icon name="calendar" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={{borderColor: '#303030'}}>
                                <Text  style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}>Başlangıc Tarihi</Text>
                            </Body>
                            <Right style={{borderColor: '#303030'}}>
                                <DatePicker defaultDate={minDate} minimumDate={minDate} maximumDate={maxDate}
                                    locale={"tr"} timeZoneOffsetInMinutes={undefined}
                                    modalTransparent={false}
                                    animationType={"fade"} placeHolderText={showDefaultDate()}
                                    textStyle={{ color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}
                                    placeHolderTextStyle={{ color: "#d3d3d3" }}
                                    onDateChange={this.setDate} disabled={false}
                                />
                            </Right>
                        </ListItem>
                        <ListItem icon>
                            <Left>
                                <Icon name="clock" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={{borderColor: '#303030'}}>
                                <Text  style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}>Başlangıc Saati</Text>
                            </Body>
                            <Right style={{borderColor: '#303030'}}>
                                <Text style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular', marginRight: 8}}
                                      onPress={() => this.setState({isTimeVisible: true})}>
                                        {this.state.selectedTime.getHours() + ':' + this.state.selectedTime.getMinutes()}
                                </Text>
                            </Right>
                            <DateTimePicker
                                isVisible={this.state.isTimeVisible}
                                onConfirm={(time) => {this.setState({selectedTime: time, isTimeVisible: false})}}
                                onCancel={() => this.setState({isTimeVisible: false})}
                                mode='time'
                            />
                        </ListItem>
                    </View>

                    <View style={{marginBottom: 16, backgroundColor: '#303030'}}>
                        <ListItem icon>
                            <Left>
                                <Icon name="dollar-sign" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={{borderColor: '#303030'}}>
                                <Text style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}>Tahmini Kazanç</Text>
                            </Body>
                            <Right style={{borderColor: '#303030'}}>
                                <Text style={{color: 'green', fontFamily: 'GoogleSans-Regular', marginRight: 8}}>{this.state.income} ₺</Text>
                            </Right>
                        </ListItem>
                    </View>

                    <Button rounded block style={{backgroundColor: '#7F00FF'}} onPress={this.setTournament.bind(this)}
                            disabled={this.state.loading}>
                        <Text style={{fontFamily: 'GoogleSans-Regular'}}>OLUŞTUR</Text>
                    </Button>
                </View>
            </ScrollView>
        );
    }
}