import React, {Component} from 'react';
import {ScrollView, View} from 'react-native';
import {Tab, Tabs} from "native-base";
import LoadingDialog from "../LoadingDialog";
import TournamentsList from '../TournamentsList.js';

export default class StartPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: false,
            activeTournaments: []
        };
    }

    render() {
        return (
            <ScrollView>
                <LoadingDialog loading={this.state.loading}/>
                <View>
                    <Tabs>
                        <Tab heading="Aktif" tabStyle={{backgroundColor: '#303030'}}
                             style={{backgroundColor: '#000'}}
                             textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                             activeTabStyle={{backgroundColor: '#303030'}}
                             activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                            <TournamentsList loading={this.state.loading} tournaments={this.state.activeTournaments} />
                        </Tab>
                        <Tab heading="Kayıtlı" tabStyle={{backgroundColor: '#303030'}}
                             style={{backgroundColor: '#000'}}
                             textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                             activeTabStyle={{backgroundColor: '#303030'}}
                             activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        </Tab>
                        <Tab heading="Bitmiş" tabStyle={{backgroundColor: '#303030'}}
                             style={{backgroundColor: '#000'}}
                             textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                             activeTabStyle={{backgroundColor: '#303030'}}
                             activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        </Tab>
                    </Tabs>
                </View>
            </ScrollView>
        )
    }
}