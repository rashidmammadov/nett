import React, { Component } from 'react';
import {AppRegistry, StyleSheet, Text, View, TextInput, Button} from 'react-native';

export default class SignPage extends Component {

	constructor(props) {
		super(props);
		this.navigator = this.props.navigator;
	}

	goLogin() {

	}

	render() {
		return (
			<View
				style={{
					flex: 1,
					flexDirection: 'column',
			        justifyContent: 'center',
			        padding: 15
				}}>
				<View>
					<TextInput
						style={{
							height: 50
						}}
						placeholder="Kullanıcı adı"/>
				</View>
				<View>
					<TextInput
						style={{
							height: 50
						}}
						placeholder="Şifre"/>
				</View>
				<View
					style={{
						height: 50
					}}>
					<Button
					  	title="Giriş"
					  	color="#4285f4"
					  	onPress={this.goLogin.bind(this)} />
				</View>
			</View>
		);
	}
}