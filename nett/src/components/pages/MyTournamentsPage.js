import React, {Component} from 'react';
import {View} from 'react-native';
import {Tab, Tabs} from "native-base";
import LoadingDialog from "../LoadingDialog";
import TournamentsList from '../TournamentsList.js';
import {SUCCESS} from "../../services/Constants";
import {getMyTournaments} from "../../services/TournamentService";
import {errorToast, warningToast} from "../../services/ToastService";
import {style} from "../../assets/style/Custom";
import {googleTrack} from "../../services/GoogleAnalytics";

export default class StartPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: false,
            initialPage: this.props.initialPage || 0,
            tournaments: [{
                loaded: false,
                data: [],
                status: 1,
            },{
                loaded: false,
                data: [],
                status: 2,
            },{
                loaded: false,
                data: [],
                status: 0
            }]
        };

        this.getSelectedTabsResult = this.getSelectedTabsResult.bind(this);
    }

    componentDidMount() {
        this.getSelectedTabsResult(0);
    }

    getSelectedTabsResult(index) {
        if (!this.state.tournaments[index].loaded) {
            let data = {'status': this.state.tournaments[index].status};
            this.setState({loading: true});
            googleTrack('My Tournaments Page', ('send request to get my tournaments with status: ' + this.state.tournaments[index].status));
            getMyTournaments(data)
                .then((res) => {
                    this.setState({loading: false});
                    if (res.status === SUCCESS) {
                        let d = Object.assign({}, this.state.tournaments);
                        d[index].loaded = true;
                        d[index].data = res.data;
                        this.setState({d});
                        googleTrack('My Tournaments Page', 'response of ' + (this.state.tournaments[index].status) + ' tournaments');
                    } else {
                        warningToast(res.message);
                        googleTrack('My Tournaments Page', 'warning of ' + (this.state.tournaments[index].status) + ' tournaments', res.message);
                    }
                })
                .catch((error) => {
                    this.setState({loading: false});
                    errorToast(error.message);
                    googleTrack('My Tournaments Page', 'error of ' + (this.state.tournaments[index].status) + ' tournaments', error.message);
                });
        }
    }

    render() {
        return (
            <View style={style.flex}>
                <LoadingDialog loading={this.state.loading}/>
                <Tabs initialPage={this.state.initialPage} onChangeTab={(tab) => this.getSelectedTabsResult(tab.i)}>
                    <Tab heading="Aktif" tabStyle={style.secondaryBGColor}
                         style={style.primaryBGColor}
                         textStyle={style.tabTextStyle}
                         activeTabStyle={style.secondaryBGColor}
                         activeTextStyle={style.tabActiveTextStyle}>
                        <TournamentsList loading={!this.state.tournaments[0].loaded} tournaments={this.state.tournaments[0].data} />
                    </Tab>
                    <Tab heading="Bekleyen" tabStyle={style.secondaryBGColor}
                         style={style.primaryBGColor}
                         textStyle={style.tabTextStyle}
                         activeTabStyle={style.secondaryBGColor}
                         activeTextStyle={style.tabActiveTextStyle}>
                        <TournamentsList loading={!this.state.tournaments[1].loaded} tournaments={this.state.tournaments[1].data} />
                    </Tab>
                    <Tab heading="Bitmiş" tabStyle={style.secondaryBGColor}
                         style={style.primaryBGColor}
                         textStyle={style.tabTextStyle}
                         activeTabStyle={style.secondaryBGColor}
                         activeTextStyle={style.tabActiveTextStyle}>
                        <TournamentsList loading={!this.state.tournaments[2].loaded} tournaments={this.state.tournaments[2].data} />
                    </Tab>
                </Tabs>
            </View>
        )
    }
}