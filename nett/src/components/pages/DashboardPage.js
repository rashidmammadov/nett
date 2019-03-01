import React, { Component } from 'react';
import {ImageBackground, ScrollView, View} from 'react-native';
import {Text} from 'native-base';
import StandingsChart from '../charts/StandingsChart.js';

export default class DashboardPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            data: [ -26, -15, -20, -5, -2, -2, -8 ]
        };
    }

	render() {
		return (
            <ScrollView style={{padding: 16}}>
                <View style={{flex: 1, flexDirection: 'row', justifyContent: 'space-between', marginBottom: 16}}>
                    <Text style={{color: '#f8f8f8', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>rashidmammadov</Text>
                    <Text style={{color: '#f8f8f8', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>sıralama: #41257</Text>
                </View>

                <View style={{flexDirection: 'row', marginBottom: 16}}>
                    <View transparent style={{flex: 1, marginRight: 8}}>
                        <ImageBackground source={require('../../assets/images/balance.png')}
                             style={{ flex: 1, height: 72, width: null, resizeMode: 'cover', padding: 16}}>
                            <Text style={{color: '#303030', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Bakiye</Text>
                            <Text style={{color: '#303030', fontSize: 20, fontFamily: 'GoogleSans-Regular'}}>85 ₺</Text>
                        </ImageBackground>
                    </View>
                    <View transparent style={{flex: 1, marginLeft: 8}}>
                        <ImageBackground source={require('../../assets/images/coupon.png')}
                             style={{ flex: 1, height: 72, width: null, resizeMode: 'cover', padding: 16}}>
                            <Text style={{color: '#303030', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Turnuva Bileti</Text>
                            <Text style={{color: '#303030', fontSize: 20, fontFamily: 'GoogleSans-Regular'}}>2 adet</Text>
                        </ImageBackground>
                    </View>
                </View>

                <View style={{marginBottom: 16}}>
                    <Text style={{color: '#f8f8f8', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Son 10 Turnuva Sıralamam</Text>
                    <StandingsChart data={this.state.data} />
                </View>

                <Text style={{color: '#f8f8f8', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Turnuvalarım</Text>
                <View style={{flexDirection: 'row', marginBottom: 16}}>
                    <View transparent style={{flex: 1, marginRight: 8}}>
                        <ImageBackground source={require('../../assets/images/active-tournament.png')}
                                         style={{ flex: 1, height: 64, width: null, resizeMode: 'cover', padding: 16}}>
                            <Text style={{color: '#d3d3d3', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Aktif</Text>
                            <Text style={{color: '#d3d3d3', fontSize: 20, fontFamily: 'GoogleSans-Regular'}}>1</Text>
                        </ImageBackground>
                    </View>
                    <View transparent style={{flex: 1, marginLeft: 8, marginRight: 8}}>
                        <ImageBackground source={require('../../assets/images/registered-tournament.png')}
                                         style={{ flex: 1, height: 64, width: null, resizeMode: 'cover', padding: 16}}>
                            <Text style={{color: '#d3d3d3', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Kayıtlı</Text>
                            <Text style={{color: '#d3d3d3', fontSize: 20, fontFamily: 'GoogleSans-Regular'}}>2</Text>
                        </ImageBackground>
                    </View>
                    <View transparent style={{flex: 1, marginLeft: 8}}>
                        <ImageBackground source={require('../../assets/images/ended-tournament.png')}
                                         style={{ flex: 1, height: 64, width: null, resizeMode: 'cover', padding: 16}}>
                            <Text style={{color: '#d3d3d3', fontSize: 14, fontFamily: 'GoogleSans-Regular'}}>Bitmiş</Text>
                            <Text style={{color: '#d3d3d3', fontSize: 20, fontFamily: 'GoogleSans-Regular'}}>4</Text>
                        </ImageBackground>
                    </View>
                </View>
            </ScrollView>
		);
	}
}