import React, { Component } from 'react';
import {AsyncStorage, ScrollView} from 'react-native';
import {Root, Container, Footer, Item, Input, Button, Text, Toast} from 'native-base';
import {Actions} from 'react-native-router-flux';
import {signIn} from '../../services/SignService.js';
import {setUser} from "../../services/ConfigService";
import {errorToast, warningToast} from "../../services/ToastService";
import {ONESIGNAL_APPID, RESET, SUCCESS, USER_STORAGE} from "../../services/Constants";

export default class LoginPage extends Component {

	constructor(props) {
		super(props);
        this.state = {
            loading: false,
            deviceId: null
        };
		this.navigator = this.props.navigator;
	}

    async componentDidMount() {
        AsyncStorage.getItem(ONESIGNAL_APPID).then((deviceId) => {
            this.setState({deviceId: deviceId});
        });
    }

	login() {
	    let user = {
            email: this.state.email,
            password: this.state.password,
            onesignalDeviceId: this.state.deviceId
        };
        this.setState({loading: true});
        signIn(user)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    setUser(res.data);
                    AsyncStorage.setItem(USER_STORAGE, JSON.stringify(res.data));
                    Actions.AppPage({type: RESET});
                } else {
                    warningToast(res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
            });
	}

	render() {
		return (
            <Root>
                <ScrollView>
                    <Container style={{ flex: 1, justifyContent: "center", alignItems: "center", padding: 16 }}>
                        <Item regular style={{marginBottom: 8}}>
                            <Input placeholder="Email"
                                onChangeText={(value) => this.setState({email: value})}
                                value={this.state.email}/>
                        </Item>
                        <Item regular style={{marginBottom: 8}}>
                            <Input placeholder="Şifre" secureTextEntry={true}
                                onChangeText={(value) => this.setState({password: value})}
                                value={this.state.password}/>
                        </Item>
                        <Button disabled={this.state.loading ? true : false} block onPress={this.login.bind(this)}>
                            <Text>GİRİŞ</Text>
                        </Button>
                        <Footer style={{position: 'absolute', bottom:0, backgroundColor: '#fff'}}>
                            <Text style={{marginTop: 16}}>Bir hesabın yok mu?
                                <Text onPress={Actions.RegisterPage} style={{fontWeight: 'bold'}}> Kayıt ol</Text>.
                            </Text>
                        </Footer>
                    </Container>
                </ScrollView>
            </Root>
		);
	}
}