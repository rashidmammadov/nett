import React, { Component } from 'react';
import {AsyncStorage, AppRegistry, FlatList, StyleSheet, Text, View, TextInput, Button} from 'react-native';
import {Container, Header, Content, Form, Item, Input, Label} from 'native-base';
import {Actions} from 'react-native-router-flux';
import {signIn} from '../../services/SignService.js';

export default class SignPage extends Component {

	constructor(props) {
		super(props);
		this.state = {user: ''};
		this.navigator = this.props.navigator;
	}

	login() {
	    let loginData = {
            email: this.state.loginEmail,
            password: this.state.loginPassword
        }
        signIn(loginData).then((res) => {
            AsyncStorage.setItem('token', res.data.remember_token);
            Actions.DashboardPage({type: 'reset'});
        });
	}

	render() {
		return (
		    <Container>
                <Content style={{flex: 1}}>
                    <Form>
                        <Item rounded>
                            <Input placeholder="Email"
                                onChangeText={(value) => this.setState({loginEmail: value})}
                                value={this.state.loginEmail}/>
                        </Item>
                        <Item rounded>
                            <Input placeholder="Şifre"
                                onChangeText={(value) => this.setState({loginPassword: value})}
                                value={this.state.loginPassword}/>
                        </Item>
                        <Text>{this.state.user.remember_token}</Text>
                        <Button title="Giriş" color="#4285f4" onPress={this.login.bind(this)} />
                    </Form>
                </Content>
            </Container>
		);
	}
}