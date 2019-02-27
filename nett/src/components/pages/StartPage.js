import React, {Component} from 'react';
import {AsyncStorage, Text, Button, View} from 'react-native';
import {Actions} from 'react-native-router-flux';
import {style} from '../../assets/style/Custom.js';

export default class StartPage extends Component {

	constructor(props) {
		super(props);
		//AsyncStorage.setItem('token', 'rashid');
		//AsyncStorage.removeItem('token');
	}

	async componentDidMount() {
	    AsyncStorage.getItem('token').then((value) => {
	        if (value) {
	            Actions.AppPage({type: 'reset'});
	        } else {
	            Actions.LoginPage({type: 'reset'});
	        }
	    });
	}

	render() {
        return (
            <View style={style.columnCenter}>
                <Text>Loading..</Text>
            </View>
        );
	}
}