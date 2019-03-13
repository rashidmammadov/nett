import React, { Component } from 'react';
import {AsyncStorage, ScrollView} from 'react-native';
import {Root, Container, Footer, Item, Input, Picker, Button, Text, Toast} from 'native-base';
import {Actions} from 'react-native-router-flux';
import {regions} from '../../services/DataService.js';
import {signUp} from '../../services/SignService.js';

export default class RegisterPage extends Component {

	constructor(props) {
		super(props);
		this.state = {
		    loading: true,
		    regions: null,
		    cities: [],
		    selectedCity: null,
		    districts: [],
		    selectedDistrict: null
		};
		this.getRegions.bind();
		this.getRegions();
	}

	getRegions() {
	    regions().then((res) => {
	        this.setState({loading: false});
	        if (res.status === 'success') {
	            this.setState({
	                regions: res.data.regions,
	                cities: Object.keys(res.data.regions)
	            });
	            this.setState({
	                districts: res.data.regions[this.state.cities[34]]
	            });
	            this.setState({
	                selectedCity: this.state.cities[34],
	                selectedDistrict: this.state.districts[0]
	            });
	        }
	    })
	}

	register() {
	    let user = {
	        type: 'player',
	        username: this.state.username,
            email: this.state.email,
            password: this.state.password,
            passwordConfirmation: this.state.passwordConfirmation,
            city: this.state.selectedCity,
            district: this.state.selectedDistrict
        };
        signUp(user).then((res) => {
            if (res.status === 'success') {
                AsyncStorage.setItem('token', res.data.remember_token);
                Actions.AppPage({type: 'reset'});
                Toast.show({
                    text: 'Hesap başarıyla oluşturuldu',
                    buttonText: 'tamam',
                    type: 'success'
                });
            } else {
                Toast.show({
                    text: res.message,
                    buttonText: 'tamam',
                    type: 'danger'
                });
            }
        });
	}

	onCityChange(value) {
        this.setState({
            selectedCity: value,
            districts: this.state.regions[value]
        });
        this.setState({
            selectedDistrict: this.state.districts[0]
        });
	}

	onDistrictChange(value) {
        this.setState({
            selectedDistrict: value
        });
    }

	render() {
	    let cityPickers = this.state.cities.map( (city, i) => {
            return <Picker.Item key={i} value={city} label={city} />
        });

        let districtPickers = this.state.districts.map( (district, i) => {
            return <Picker.Item key={i} value={district} label={district} />
        });

		return (
            <Root>
                <ScrollView>
                    <Container style={{ flex: 1, justifyContent: "center", alignItems: "center", padding: 16 }}>
                        <Item regular style={{marginBottom: 8}}>
                            <Input placeholder="Kullanıcı Adı"
                                onChangeText={(value) => this.setState({username: value})}
                                value={this.state.username}/>
                        </Item>
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
                        <Item regular style={{marginBottom: 8}}>
                            <Input placeholder="Şifre Tekrarı" secureTextEntry={true}
                                onChangeText={(value) => this.setState({passwordConfirmation: value})}
                                value={this.state.passwordConfirmation}/>
                        </Item>
                        <Item picker regular style={{marginBottom: 8}}>
                            <Picker
                                mode="dropdown"
                                placeholder="Şehir"
                                selectedValue={this.state.selectedCity}
                                onValueChange={this.onCityChange.bind(this)}
                                >
                                {cityPickers}
                            </Picker>
                        </Item>
                        <Item picker regular style={{marginBottom: 8}}>
                            <Picker
                                mode="dropdown"
                                placeholder="İlçe"
                                selectedValue={this.state.selectedDistrict}
                                onValueChange={this.onDistrictChange.bind(this)}
                                >
                                {districtPickers}
                            </Picker>
                        </Item>
                        <Button disabled={this.state.loading ? true : false} block onPress={this.register.bind(this)}>
                            <Text>KAYIT OL</Text>
                        </Button>
                        <Footer style={{position: 'absolute', bottom:0, backgroundColor: '#fff'}}>
                            <Text style={{marginTop: 16}}>Zaten bir hesabın var mı?
                                <Text onPress={Actions.LoginPage} style={{fontWeight: 'bold'}}> Giriş yap</Text>.
                            </Text>
                        </Footer>
                    </Container>
                </ScrollView>
            </Root>
		);
	}
}