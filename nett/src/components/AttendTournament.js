import React, {Component} from 'react';
import {View} from 'react-native';
import {Button, Text} from 'native-base';
import Dialog, { DialogContent } from 'react-native-popup-dialog';
import {attend, leave} from "../services/ParticipantService";
import {errorToast, successToast, warningToast} from "../services/ToastService";
import {style} from "../assets/style/Custom";
import {user} from "../services/ConfigService";
import {
    MONEY,
    SUCCESS,
    TICKET,
    TOURNAMENT_STATUS_ACTIVE, TOURNAMENT_STATUS_CANCEL,
    TOURNAMENT_STATUS_CLOSE,
    TOURNAMENT_STATUS_OPEN
} from "../services/Constants";

export default class AttendTournament extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: false,
            visible: false,
            data: this.props.data
        };
    }

    attendWithMoney() {
        let params = {
            tournamentId: this.state.data.tournamentId,
            paymentType: MONEY
        };
        this.$$attend(params);
    }

    attendWithTicket() {
        let params = {
            tournamentId: this.state.data.tournamentId,
            paymentType: TICKET
        };
        this.$$attend(params);
    }

    leave() {
        let params = {
            tournamentId: this.state.data.tournamentId
        };
        this.$$leave(params);
    }

    $$attend(params) {
        this.setState({loading: true});
        this.setState({visible: false});
        attend(params)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    let data = Object.assign({}, this.state.data);
                    data.attended = true;
                    data.currentParticipants = res.data.currentParticipants;
                    this.setState({data});
                    successToast(res.message);
                } else {
                    warningToast(res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
            });
    }

    $$leave(params) {
        this.setState({loading: true});
        this.setState({visible: false});
        leave(params)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    let data = Object.assign({}, this.state.data);
                    data.attended = false;
                    data.currentParticipants = res.data.currentParticipants;
                    this.setState({data});
                    successToast(res.message);
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
        let getStatus = () => {
            let resut = {color: '', name: ''};
            let status = this.state.data.status;
            if (status === TOURNAMENT_STATUS_CLOSE) {
                resut.color = '#707070';
                resut.name = 'Bitmiş';
            } else if (status === TOURNAMENT_STATUS_ACTIVE) {
                resut.color = 'red';
                resut.name = 'Aktif';
            } else if (status === TOURNAMENT_STATUS_OPEN) {
                resut.color = 'green';
                resut.name = 'Bekleyen';
            } else if (status === TOURNAMENT_STATUS_CANCEL) {
                resut.color = 'gold';
                resut.name = 'İptal';
            }
            return resut;
        };

        if (user && Number(user.id) === Number(this.state.data.holder.id)) {
            return (
                <View>
                    <Button small style={{backgroundColor: getStatus().color}}>
                        <Text uppercase={false} style={style.fontFamily}>{getStatus().name}</Text>
                    </Button>
                    <Text note style={style.fontFamily}>
                        {this.state.data.participantCount - this.state.data.currentParticipants} yer kaldı
                    </Text>
                </View>
            )
        } else if (this.state.data.attended === true) {
            return (
                <View>
                    <Button small success onPress={() => { this.leave() }}>
                        <Text uppercase={false} style={style.fontFamily}>Katıldın</Text>
                    </Button>
                    <Text note style={style.fontFamily}>
                        {this.state.data.participantCount - this.state.data.currentParticipants} yer kaldı
                    </Text>
                </View>
            )
        } else {
            return (
                <View>
                    <Button small light onPress={() => { this.setState({visible: true}); }}>
                        <Text uppercase={false} style={style.fontFamily}>Katıl</Text>
                    </Button>
                    <Text note style={style.fontFamily}>
                        {this.state.data.participantCount - this.state.data.currentParticipants} yer kaldı
                    </Text>
                    <Dialog visible={this.state.visible} onTouchOutside={() => { this.setState({visible: false}); }}>
                        <DialogContent style={style.margin16}>
                            <Text style={[style.secondaryTextColor, style.fontFamily, style.bold]}>
                                Turnuvaya Ödemeyi Nasıl Yapacaksın?
                            </Text>
                            <View style={[style.alignRow, style.marginTop16, style.spaceBetween]}>
                                <Button small rounded style={style.customBGColor} onPress={() => this.attendWithMoney()}>
                                    <Text uppercase={false} style={[style.primaryTextColor, style.fontFamily]}>Hesabımdan</Text>
                                </Button>
                                <Button small rounded style={style.customBGColor} onPress={() => this.attendWithTicket()}>
                                    <Text uppercase={false} style={[style.primaryTextColor, style.fontFamily]}>Bilet Kullanarak</Text>
                                </Button>
                            </View>
                        </DialogContent>
                    </Dialog>
                </View>
            )
        }
    }

}