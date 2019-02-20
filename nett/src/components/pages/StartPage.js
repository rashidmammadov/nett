import React, {Component} from 'react';
import {Text, Button, View} from 'react-native';
import {Actions} from 'react-native-router-flux';
import Cookie from 'react-native-cookie';

export default class StartPage extends Component {

	constructor(props) {
		super(props);
		Cookie.set('http://bing.com/', 'foo', 'bar').then(() => console.log(success));
		Cookie.get('http://bing.com/', 'foo')
		    .then((cookie) => console.log(cookie) );
	}

	render() {
		return (
			<View
				style={{
					flex: 1,
					flexDirection: 'column',
					alignItems: 'center',
					justifyContent: 'center'
				}}>
				<Text onPress={Actions.DashboardPage}>{this.state.cookie}Yükleniyor..</Text>
			</View>
		);
	}
}