import React, {Component} from 'react';
import {View, ScrollView} from 'react-native';
import {Tab, Tabs, Text} from 'native-base';
import DepositTab from './tabs/DepositTab.js';

export default class BudgetPage extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <ScrollView>
                <View style={{height: 64, backgroundColor: '#7F00FF'}}>
                </View>
                <View style={{padding: 16, marginTop: -64}}>
                    <Text style={{color: '#f8f8f8', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Bakiye: 85 ₺</Text>
                    <Tabs>
                        <Tab heading="Para Yatır" tabStyle={{backgroundColor: '#303030'}}
                             textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                             activeTabStyle={{backgroundColor: '#303030'}}
                             activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                            <DepositTab />
                        </Tab>
                        <Tab heading="Para Çek" tabStyle={{backgroundColor: '#303030'}}
                             textStyle={{color: '#a3a3a3', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}
                             activeTabStyle={{backgroundColor: '#303030'}}
                             activeTextStyle={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular', fontWeight: 'normal'}}>
                        </Tab>
                    </Tabs>
                </View>
            </ScrollView>
        );
    }
}