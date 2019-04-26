import React, {Component} from 'react';
import {AsyncStorage, Text, View} from 'react-native';
import {Actions} from 'react-native-router-flux';
import {refreshUser} from "../../services/SignService";
import {setUser} from "../../services/ConfigService";
import {errorToast,  warningToast} from "../../services/ToastService";
import {style} from '../../assets/style/Custom.js';
import {DEACTIVE_USER_STATE, RESET, SUCCESS, USER_STORAGE} from "../../services/Constants";
import {googleTrack} from "../../services/GoogleAnalytics";

export default class StartPage extends Component {

	constructor(props) {
		super(props);
		//AsyncStorage.removeItem(USER_STORAGE);
	}

	async componentDidMount() {
	    AsyncStorage.getItem(USER_STORAGE).then((value) => {
			googleTrack('Start Page', 'get user data from storage', value);
	        if (value) {
	            this.refreshToken();
	        } else {
				googleTrack('Start Page', 'redirect to login page');
	            Actions.LoginPage({type: RESET});
	        }
	    });
	}

	refreshToken() {
		googleTrack('Start Page', 'send request to refresh token');
		refreshUser()
			.then((res) => {
				if (res.status === SUCCESS) {
					this.$$userRefreshedSuccessfully(res);
				} else {
					this.$$userRefreshedWarning(res);
				}
			})
			.catch((error) => {
				this.$$userRefreshedError(error);
			});
	}

	$$userRefreshedSuccessfully(res) {
		setUser(res.data);
		AsyncStorage.setItem(USER_STORAGE, JSON.stringify(res.data));
		googleTrack('Start Page', 'response of refreshed token', res.data);

		if (Number(res.data.state) === DEACTIVE_USER_STATE) {
			googleTrack('Start Page', 'redirect to activate profile page', res.data);
			Actions.ActivateProfilePage({user: res.data, type: RESET});
		} else {
			this.props.renderPage();
		}
	}

	$$userRefreshedWarning(res) {
		AsyncStorage.removeItem(USER_STORAGE);
		googleTrack('Start Page', 'warning of refreshed token', res.message);
		Actions.LoginPage({type: RESET});
		warningToast(res.message);
	}

	$$userRefreshedError(error) {
		errorToast(error.message);
		googleTrack('Start Page', 'error of refreshed token', error.message);
	}

	render() {
        return (
            <View style={style.columnCenter}>
                <Text>Loading..</Text>
            </View>
        );
	}
}