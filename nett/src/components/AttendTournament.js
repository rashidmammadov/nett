import React, {Component} from 'react';
import {View} from 'react-native';
import {Button, Text, Toast} from 'native-base';
import Dialog, { DialogContent } from 'react-native-popup-dialog';
import {attend, leave} from "../services/ParticipantService";

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
            paymentType: 'money'
        };
        this.$$attend(params);
    }

    attendWithTicket() {
        let params = {
            tournamentId: this.state.data.tournamentId,
            paymentType: 'ticket'
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
        attend(params).then((res) => {
            this.setState({loading: false});
            if (res.status === 'success') {
                let data = Object.assign({}, this.state.data);
                data.attended = true;
                data.currentParticipants = res.data.currentParticipants;
                this.setState({data});

                Toast.show({
                    text: res.message,
                    buttonText: 'tamam',
                    type: 'success'
                });
            } else {
                Toast.show({
                    text: res.message,
                    buttonText: 'tamam',
                    type: 'warning'
                });
            }
        });
    }

    $$leave(params) {
        this.setState({loading: true});
        this.setState({visible: false});
        leave(params).then((res) => {
            this.setState({loading: false});
            if (res.status === 'success') {
                let data = Object.assign({}, this.state.data);
                data.attended = false;
                data.currentParticipants = res.data.currentParticipants;
                this.setState({data});

                Toast.show({
                    text: res.message,
                    buttonText: 'tamam',
                    type: 'success'
                });
            } else {
                Toast.show({
                    text: res.message,
                    buttonText: 'tamam',
                    type: 'warning'
                });
            }
        });
    }

    render() {
        if (this.state.data.attended) {
            return (
                <View>
                    <Button small success onPress={() => { this.leave() }}>
                        <Text uppercase={false} style={{fontFamily: 'GoogleSans-Regular'}}>Katıldın</Text>
                    </Button>
                    <Text note style={{fontFamily: 'GoogleSans-Regular'}}>
                        {this.state.data.participantCount - this.state.data.currentParticipants} yer kaldı
                    </Text>
                </View>
            )
        } else {
            return (
                <View>
                    <Button small light onPress={() => { this.setState({visible: true}); }}>
                        <Text uppercase={false} style={{fontFamily: 'GoogleSans-Regular'}}>Katıl</Text>
                    </Button>
                    <Text note style={{fontFamily: 'GoogleSans-Regular'}}>
                        {this.state.data.participantCount - this.state.data.currentParticipants} yer kaldı
                    </Text>
                    <Dialog visible={this.state.visible} onTouchOutside={() => { this.setState({visible: false}); }}>
                        <DialogContent style={{margin: 16}}>
                            <Text style={{color: '#303030', fontFamily: 'GoogleSans-Regular', fontWeight: 'bold'}}>
                                Turnuvaya Ödemeyi Nasıl Yapacaksın?
                            </Text>
                            <View style={{flexDirection: 'row', marginTop: 16, justifyContent: 'space-between'}}>
                                <Button small primary onPress={() => this.attendWithMoney()}>
                                    <Text uppercase={false} style={{fontFamily: 'GoogleSans-Regular'}}>Hesabımdan</Text>
                                </Button>
                                <Button small primary onPress={() => this.attendWithTicket()}>
                                    <Text uppercase={false} style={{fontFamily: 'GoogleSans-Regular'}}>Bilet
                                        Kullanarak</Text>
                                </Button>
                            </View>
                        </DialogContent>
                    </Dialog>
                </View>
            )
        }
    }

}