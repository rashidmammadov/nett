import React, {Component} from 'react';
import {View} from 'react-native';
import TournamentsList from '../TournamentsList.js';
import {search} from "../../services/TournamentService";
import {SUCCESS, TOURNAMENT_STATUS_OPEN} from "../../services/Constants";
import {errorToast, warningToast} from "../../services/ToastService";
import LoadingDialog from "../LoadingDialog";
import {style} from "../../assets/style/Custom";
import {googleTrack} from "../../services/GoogleAnalytics";

export default class SearchPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: true,
            tournaments: []
        };
        this.init();
    }

    init() {
        let data = {'status': TOURNAMENT_STATUS_OPEN};
        googleTrack('Search Page', 'send request to get search result', 'status: '.data);
        search(data)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    this.setState({tournaments: res.data});
                    googleTrack('Search Page', 'response of search tournament', res.message);
                } else {
                    warningToast(res.message);
                    googleTrack('Search Page', 'warning of search tournament', res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
                googleTrack('Search Page', 'error of search tournament', error.message);
            });
    }

    render() {
        return (
            <View style={style.flex}>
                <LoadingDialog loading={this.state.loading} />
                <TournamentsList loading={this.state.loading} tournaments={this.state.tournaments} />
            </View>
        );
    }
}