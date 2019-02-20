import React, {Component} from 'react';
import {AsyncStorage, Text, Button, View} from 'react-native';
import {Actions} from 'react-native-router-flux';

export default class StartPage extends Component {

	constructor(props) {
		super(props);
		//AsyncStorage.setItem('user', 'rashid');
		//AsyncStorage.removeItem('user');
	}

	async componentDidMount() {
	    AsyncStorage.getItem('user').then((value) => {
	        if (value) {
	            Actions.DashboardPage({type: 'reset'});
	        } else {
	            Actions.SignPage({type: 'reset'});
	        }
	    });
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
                <Text>Loading..</Text>
            </View>
        );
	}
}