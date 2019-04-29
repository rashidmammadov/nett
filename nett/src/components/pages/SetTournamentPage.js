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
import {style} from "../../assets/style/Custom";
import {googleTrack} from "../../services/GoogleAnalytics";

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
        googleTrack('Set Tournament Page', 'send request to get games');
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
                    googleTrack('Set Tournament Page', 'response of game data', res.data);
                } else {
                    warningToast(res.message);
                    googleTrack('Set Tournament Page', 'warning of game data', res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
                googleTrack('Set Tournament Page', 'error of game data', error.message);
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
        googleTrack('Set Tournament Page', 'send request to set tournament', data);
        add(data)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    successToast(res.message);
                    googleTrack('Set Tournament Page', 'response of set tournament', res.message);
                } else {
                    warningToast(res.message);
                    googleTrack('Set Tournament Page', 'warning of set tournament', res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
                googleTrack('Set Tournament Page', 'error of set tournament', error.message);
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
            let select = <View style={style.actionSheetView}>
                            <Thumbnail square small source={{uri: game.gameImage}}/>
                <Text style={style.actionSheetText}>{game.gameName}</Text>
                        </View>;
            let cancel = <Text style={style.actionSheetCancel}>{game.gameName}</Text>;
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
                <View style={[style.height64, style.customBGColor]}>
                </View>
                <View style={[style.padding16, style.innerMarginTop]}>
                    <Text style={[style.primaryTextColor, style.fontFamily]}>Turnuva Düzenle</Text>
                    <View style={[style.marginBottom16, style.secondaryBGColor]}>
                        <ListItem icon onPress={() => this.SelectGame.show()}>
                            <Left>
                                <Icon name="hash" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={style.borderColor}>
                            <Text style={[style.colorLight, style.fontFamily]}>Oyun</Text>
                            </Body>
                            <Text style={[style.colorLight, style.fontFamily, style.marginRight24]}>
                                {this.state.selectedGame && this.state.selectedGame.gameName}
                            </Text>
                        </ListItem>
                        <ListItem icon>
                            <Left>
                                <Icon name="list" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={style.borderColor}>
                            <Text style={[style.colorLight, style.fontFamily]}>Turnuva Tipi</Text>
                            </Body>
                            <Picker
                                mode="dropdown"
                                placeholder="Oyun"
                                style={[style.colorLight, style.fontFamily]}
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
                            <Body style={style.borderColor}>
                            <Text style={[style.colorLight, style.fontFamily]}>Katılımcı Sayısı</Text>
                            </Body>
                            <Picker
                                mode="dropdown"
                                placeholder="Oyun"
                                style={[style.colorLight, style.fontFamily]}
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
                            <Body style={style.borderColor}>
                            <Text style={[style.colorLight, style.fontFamily]}>Başlangıç Tarihi</Text>
                            </Body>
                            <Right style={style.borderColor}>
                                <DatePicker defaultDate={minDate} minimumDate={minDate} maximumDate={maxDate}
                                            locale={"tr"} timeZoneOffsetInMinutes={undefined}
                                            modalTransparent={false}
                                            animationType={"fade"} placeHolderText={showDefaultDate()}
                                            textStyle={[style.colorLight, style.fontFamily]}
                                            placeHolderTextStyle={style.colorLight}
                                            onDateChange={this.setDate} disabled={false}
                                />
                            </Right>
                        </ListItem>
                        <ListItem icon>
                            <Left>
                                <Icon name="clock" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={style.borderColor}>
                            <Text style={[style.colorLight, style.fontFamily]}>Başlangıç Saati</Text>
                            </Body>
                            <Right style={style.borderColor}>
                                <Text style={[style.colorLight, style.fontFamily, style.marginRight8]}
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

                    <View style={[style.marginBottom16, style.secondaryBGColor]}>
                        <ListItem icon>
                            <Left>
                                <Icon name="dollar-sign" size={24} color={'#d3d3d3'} />
                            </Left>
                            <Body style={style.borderColor}>
                            <Text style={[style.colorLight, style.fontFamily]}>Tahmini Kazanç</Text>
                            </Body>
                            <Right style={style.borderColor}>
                                <Text
                                    style={[style.colorGreen, style.fontFamily, style.marginRight8]}>{this.state.income} ₺</Text>
                            </Right>
                        </ListItem>
                    </View>

                    <Button rounded block style={style.customBGColor}
                            onPress={() => this.setState({visible: true})}
                            disabled={this.state.loading}>
                        <Text style={style.fontFamily}>OLUŞTUR</Text>
                    </Button>
                    <Dialog visible={this.state.visible} onTouchOutside={() => { this.setState({visible: false}); }}>
                        <DialogContent style={{margin: 16}}>
                            <Text style={[style.secondaryTextColor, style.fontFamily, style.bold]}>
                                Turnuvaya oluşturduğunuz anda yayımlanacaktır. Eğer yeterli katılımcı sayısına ulaşılamazsa
                                turnuva otomatik iptal edilecek ve o ana kadar katılan tüm kullanıcıların katılım ücretleri iade edilecektir.
                            </Text>
                            <View style={[style.alignRow, style.marginTop16, style.spaceBetween]}>
                                <Button small rounded bordered onPress={() => this.setState({visible: false})}>
                                    <Text uppercase={false} style={[style.customColor, style.fontFamily]}>İptal
                                        Et</Text>
                                </Button>
                                <Button small rounded style={style.customBGColor} onPress={() => this.setTournament()}>
                                    <Text uppercase={false}
                                          style={[style.primaryTextColor, style.fontFamily]}>Onayla</Text>
                                </Button>
                            </View>
                        </DialogContent>
                    </Dialog>
                </View>
            </ScrollView>
        );
    }
}