import React, {Component} from 'react';
import {View} from 'react-native';
import LoadingDialog from "../LoadingDialog";
import TournamentsList from '../TournamentsList.js';
import {search} from "../../services/TournamentService";
import {SUCCESS, TOURNAMENT_STATUS_OPEN} from "../../services/Constants";
import {errorToast, warningToast} from "../../services/ToastService";

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