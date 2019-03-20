import React, {Component} from 'react';
import {View} from 'react-native';
import TournamentsList from '../TournamentsList.js';
import {search} from "../../services/TournamentService";
import {SUCCESS, TOURNAMENT_STATUS_OPEN} from "../../services/Constants";
import {errorToast, warningToast} from "../../services/ToastService";
import LoadingDialog from "../LoadingDialog";

export default class SearchPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: true,
            tournaments: [{
                "tournamentId": 1,
                "attended": true,
                "date": "31/03",
                "time": "12:00",
                "participantCount": 24,
                "currentParticipants": 1,
                "status": 2,
                "referenceCode": "tEEV7ltf",
                "price": {
                    "amount": 15,
                    "currency": "₺"
                },
                "game": {
                    "id": 1,
                    "name": null,
                    "image": null
                },
                "holder": {
                    "id": 1,
                    "username": "rashidmammadov",
                    "picture": null,
                    "city": "Izmir",
                    "district": "Bornova",
                    "address": null
                },
                "participants": null,
                "fixture": {}
            },
                {
                    "tournamentId": 2,
                    "attended": false,
                    "date": "31/03",
                    "time": "12:00",
                    "participantCount": 16,
                    "currentParticipants": 0,
                    "status": 2,
                    "referenceCode": null,
                    "price": {
                        "amount": 15,
                        "currency": "₺"
                    },
                    "game": {
                        "id": 2,
                        "name": null,
                        "image": null
                    },
                    "holder": {
                        "id": 1,
                        "username": "rashidmammadov",
                        "picture": null,
                        "city": "Izmir",
                        "district": "Bornova",
                        "address": null
                    },
                    "participants": null,
                    "fixture": {}
                },
                {
                    "tournamentId": 4,
                    "attended": false,
                    "date": "31/03",
                    "time": "12:00",
                    "participantCount": 32,
                    "currentParticipants": 0,
                    "status": 2,
                    "referenceCode": null,
                    "price": {
                        "amount": 15,
                        "currency": "₺"
                    },
                    "game": {
                        "id": 2,
                        "name": null,
                        "image": null
                    },
                    "holder": {
                        "id": 1,
                        "username": "rashidmammadov",
                        "picture": null,
                        "city": "Izmir",
                        "district": "Bornova",
                        "address": null
                    },
                    "participants": null,
                    "fixture": {}
                },
                {
                    "tournamentId": 5,
                    "attended": false,
                    "date": "31/03",
                    "time": "12:00",
                    "participantCount": 32,
                    "currentParticipants": 0,
                    "status": 2,
                    "referenceCode": null,
                    "price": {
                        "amount": 15,
                        "currency": "₺"
                    },
                    "game": {
                        "id": 2,
                        "name": null,
                        "image": null
                    },
                    "holder": {
                        "id": 1,
                        "username": "rashidmammadov",
                        "picture": null,
                        "city": "Izmir",
                        "district": "Bornova",
                        "address": null
                    },
                    "participants": null,
                    "fixture": {}
                }]
        };
        this.init();
    }

    init() {
        let data = {'status': TOURNAMENT_STATUS_OPEN};
        search(data)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    this.setState({tournaments: res.data});
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
            <View>
                <LoadingDialog loading={this.state.loading}/>
                <TournamentsList tournaments={this.state.tournaments}/>
            </View>
        );
    }
}