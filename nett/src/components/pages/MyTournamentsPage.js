import React, {Component} from 'react';
import {View} from 'react-native';
import {Tab, Tabs} from "native-base";
import LoadingDialog from "../LoadingDialog";
import TournamentsList from '../TournamentsList.js';
import {SUCCESS} from "../../services/Constants";
import {getMyTournaments} from "../../services/TournamentService";
import {errorToast, warningToast} from "../../services/ToastService";

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
            getMyTournaments(data)
                .then((res) => {
                    this.setState({loading: false});
                    if (res.status === SUCCESS) {
                        let d = Object.assign({}, this.state.tournaments);
                        d[index].loaded = true;
                        d[index].data = res.data;
                        this.setState({d});
                    } else {
                        warningToast(res.message);
                    }
                })
                .catch((error) => {
                    this.setState({loading: false});
                    errorToast(error.message);
                });
        }
    }

    render() {
        return (
            <View style={{flex: 1}}>
                <LoadingDialog loading={this.state.loading}/>
                <Tabs initialPage={this.state.initialPage} onChangeTab={(tab) => this.getSelectedTabsResult(tab.i)}>
                    <Tab heading="Aktif" tabStyle={{backgroundColor: '#303030'}}
                         style={{backgroundColor: '#000'}}
                         textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                         activeTabStyle={{backgroundColor: '#303030'}}
                         activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        <TournamentsList loading={!this.state.tournaments[0].loaded} tournaments={this.state.tournaments[0].data} />
                    </Tab>
                    <Tab heading="Bekleyen" tabStyle={{backgroundColor: '#303030'}}
                         style={{backgroundColor: '#000'}}
                         textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                         activeTabStyle={{backgroundColor: '#303030'}}
                         activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        <TournamentsList loading={!this.state.tournaments[1].loaded} tournaments={this.state.tournaments[1].data} />
                    </Tab>
                    <Tab heading="Bitmiş" tabStyle={{backgroundColor: '#303030'}}
                         style={{backgroundColor: '#000'}}
                         textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                         activeTabStyle={{backgroundColor: '#303030'}}
                         activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        <TournamentsList loading={!this.state.tournaments[2].loaded} tournaments={this.state.tournaments[2].data} />
                    </Tab>
                </Tabs>
            </View>
        )
    }
}