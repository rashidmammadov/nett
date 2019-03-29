import React, {Component} from 'react';
import {View, ScrollView} from 'react-native';
import {ListItem, Text, Left, Body, DatePicker, Picker, Right, Button, Thumbnail} from 'native-base';
import DateTimePicker from 'react-native-modal-datetime-picker';
import {games} from "../../services/DataService";
import {add} from "../../services/TournamentService";
import Icon from 'react-native-vector-icons/Feather';
import {SUCCESS, KNOCK_OUT, GROUP, RANKING} from "../../services/Constants";
import {errorToast, successToast, warningToast} from "../../services/ToastService";
import ActionSheet from 'react-native-actionsheet';
import LoadingDialog from "../LoadingDialog";
import Dialog, { DialogContent } from 'react-native-popup-dialog';

const typeMap = {
    knock_out: KNOCK_OUT,
    group: GROUP,
    ranking: RANKING
};

export default class SetTournamentPage extends Component {

    constructor(props) {
        super(props);
        let participantList = [];
        for (let i=16; i<=32; i++) {
            participantList.push(i.toString());
        }
        this.state = {
            loading: true,
            visible: false,
            games: [],
            gameTypes: [],
            participantList: participantList,
            selectedParticipant: participantList[0],
            selectedDate: new Date(),
            selectedTime: new Date(),
            income: (Number(participantList[0]) * (5 + Number(participantList[0]) / 11)).toFixed(2)
        };

        this.getGamesList();
        this.onGameChange = this.onGameChange.bind(this);
        this.setDate = this.setDate.bind(this);
    }

    getGamesList() {
        games()
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    let games = res.data;
                    games.push({'gameId': 0, 'gameName': 'İptal'});
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
        this.setState({loading: true, visible: false});
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

        let selectableGames = this.state.games.map((game) => {
            let select = <View style={{width: 300, marginTop: 2, flexDirection: 'row'}}>
                            <Thumbnail square small source={{uri: game.gameImage}}/>
                            <Text style={{marginLeft: 16, marginTop: 8, fontFamily: 'GoogleSans-Regular'}}>{game.gameName}</Text>
                        </View>;
            let cancel = <Text style={{marginLeft: 16, fontFamily: 'GoogleSans-Regular'}}>{game.gameName}</Text>;
            return game.gameId === 0 ? cancel : select;
        });

        let typePickers = this.state.gameTypes.map( (type, i) => {
            return <Picker.Item key={i} value={type} label={typeMap[type]} />
        });

        let participantPickers = this.state.participantList.map((count, i) => {
            return <Picker.Item key={i} value={count} label={count} />
        });

        let showDefaultDate = () => {
            return minDate.getDate() + '/' + (minDate.getMonth() + 1) + '/' + minDate.getFullYear()
        };

        return (
            <ScrollView>
                <ActionSheet ref={o => this.SelectGame = o}
                     title={'Turnuvası düzenlenecek oyunu seç'}
                     options={selectableGames}
                     cancelButtonIndex={this.state.games.length - 1}
                     onPress={(index) => {
                         index !== (this.state.games.length - 1) && this.onGameChange(this.state.games[index])
                     }}
                />

                <LoadingDialog loading={this.state.loading}/>
                <View style={{height: 64, backgroundColor: '#7F00FF'}}>
                </View>
                <View style={{padding: 16, marginTop: -64}}>
                    <Text style={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}}>Turnuva Düzenle</Text>
                    <View style={{marginBottom: 16, backgroundColor: '#303030'}}>
                        <ListItem icon onPress={() => this.SelectGame.show()}>
                            <Left>
                                <Icon name="hash" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={{borderColor: '#303030'}}>
                                <Text  style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular'}}>Oyun</Text>
                            </Body>
                            <Text style={{color: '#d3d3d3', fontFamily: 'GoogleSans-Regular', marginRight: 24}}>
                                {this.state.selectedGame && this.state.selectedGame.gameName}
                            </Text>
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

                    <Button rounded block style={{backgroundColor: '#7F00FF'}}
                            onPress={() => this.setState({visible: true})}
                            disabled={this.state.loading}>
                        <Text style={{fontFamily: 'GoogleSans-Regular'}}>OLUŞTUR</Text>
                    </Button>
                    <Dialog visible={this.state.visible} onTouchOutside={() => { this.setState({visible: false}); }}>
                        <DialogContent style={{margin: 16}}>
                            <Text style={{color: '#303030', fontFamily: 'GoogleSans-Regular', fontWeight: 'bold'}}>
                                Turnuvaya oluşturduğunuz anda yayımlanacaktır. Eğer yeterli katılımcı sayısına ulaşılamazsa
                                turnuva otomatik iptal edilecek ve o ana kadar katılan tüm kullanıcıların katılım ücretleri iade edilecektir.
                                Turnuva başlamasına 48 saat kalana kadar tarihi ileriye taşıyabilirsin.
                            </Text>
                            <View style={{flexDirection: 'row', marginTop: 16, justifyContent: 'space-between'}}>
                                <Button small rounded bordered onPress={() => this.setState({visible: false})}>
                                    <Text uppercase={false} style={{color: '#7F00FF', fontFamily: 'GoogleSans-Regular'}}>İptal Et</Text>
                                </Button>
                                <Button small rounded style={{backgroundColor: '#7F00FF'}} onPress={() => this.setTournament()}>
                                    <Text uppercase={false} style={{color: '#f0f0f0', fontFamily: 'GoogleSans-Regular'}}>Onayla</Text>
                                </Button>
                            </View>
                        </DialogContent>
                    </Dialog>
                </View>
            </ScrollView>
        );
    }
}