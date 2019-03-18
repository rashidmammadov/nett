import React, {Component} from 'react';
import {AsyncStorage, Text, View} from 'react-native';
import {Actions} from 'react-native-router-flux';
import {refreshUser} from "../../services/SignService";
import {setUser} from "../../services/ConfigService";
import {errorToast,  warningToast} from "../../services/ToastService";
import {style} from '../../assets/style/Custom.js';
import {RESET, SUCCESS, USER_STORAGE} from "../../services/Constants";

export default class StartPage extends Component {

	constructor(props) {
		super(props);
		//AsyncStorage.removeItem(USER_STORAGE);
	}

	async componentDidMount() {
	    AsyncStorage.getItem(USER_STORAGE).then((value) => {
	        if (value) {
	            this.refreshToken();
	        } else {
	            Actions.LoginPage({type: RESET});
	        }
	    });
	}

	refreshToken() {
		refreshUser()
			.then((res) => {
				if (res.status === SUCCESS) {
					setUser(res.data);
					AsyncStorage.setItem(USER_STORAGE, JSON.stringify(res.data));
					Actions.AppPage({type: RESET});
				} else {
					AsyncStorage.removeItem(USER_STORAGE);
					Actions.LoginPage({type: RESET});
					warningToast(res.message);
				}
			})
			.catch((error) => {
				errorToast(error.message);
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