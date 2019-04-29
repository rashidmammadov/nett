import React, {Component} from 'react';
import {AsyncStorage, ScrollView, View} from 'react-native';
import LoadingDialog from "../LoadingDialog";
import {style} from "../../assets/style/Custom";
import {Root, Button, Input, Item, Picker, Text, Thumbnail} from "native-base";
import {Actions} from "react-native-router-flux";
import {activate} from "../../services/SignService.js";
import {RESET, SUCCESS, USER_STORAGE} from "../../services/Constants";
import {setUser} from "../../services/ConfigService";
import {errorToast, warningToast} from "../../services/ToastService";
import {googleTrack} from "../../services/GoogleAnalytics";

const currentDate = new Date();
let days = [];
let months = [];
let years = [];

export default class ActivateProfilePage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: false,
            user: this.props.user,
            name: null,
            surname: null,
            phone: null,
            bDay: null,
            bMonth: null,
            bYear: null
        };

        for (let i = 1; i <= 31; i++) {
            days.push(i);
            i <= 12 && months.push(i);
        }

        for (let i = 1950; i <= currentDate.getFullYear(); i++) {
            years.push(i);
        }
    }

    setPhone(value) {
        this.setState({phone: value});
    }

    setName(value) {
        this.setState({name: value});
    }

    setSurname(value) {
        this.setState({surname: value});
    }

    onDayChange(value) {
        this.setState({bDay: value});
    }

    onMonthChange(value) {
        this.setState({bMonth: value});
    }

    onYearChange(value) {
        this.setState({bYear: value});
    }

    confirm() {
        let user = {
            name: this.state.name,
            surname: this.state.surname,
            phone: this.state.phone ? this.state.phone.toString() : null,
            birthday: this.$$setDate(),
        };
        if (user.name && user.surname && user.phone && user.birthday) {
            this.$$activateUser(user);
        } else {
            warningToast('Alanlar boş bırakılamaz');
        }
    }

    $$setDate() {
        let bDate = new Date(this.state.bYear, this.state.bMonth, this.state.bDay);
        return bDate.getTime();
    }

    $$activateUser(user) {
        this.setState({loading: true});
        googleTrack('Activate Profile Page', 'send request to activate profile', user);
        activate(user)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    setUser(res.data);
                    AsyncStorage.setItem(USER_STORAGE, JSON.stringify(res.data));
                    googleTrack('Activate Profile Page', 'response of activate profile', res.data);
                    Actions.AppPage({type: RESET});
                } else {
                    warningToast(res.message);
                    googleTrack('Activate Profile Page', 'warning of activate profile', res.message);
                }
            })
            .catch((error) => {
                this.setState({loading: false});
                errorToast(error.message);
                googleTrack('Activate Profile Page', 'error of activate profile', error.data);
            });
    }

    render() {
        let dayPickers = days.map((key, index) => {
            return <Picker.Item key={index} value={key} label={key.toString()}/>
        });

        let monthPickers = months.map((key, index) => {
            return <Picker.Item key={index} value={key} label={key.toString()}/>
        });

        let yearPickers = years.map((key, index) => {
            return <Picker.Item key={index} value={key} label={key.toString()}/>
        });

        return (
            <Root>
                <ScrollView style={style.primaryBGColor}>
                    <LoadingDialog loading={this.state.loading}/>
                    <View style={[style.height64, style.customBGColor]}>
                    </View>
                    <View style={[style.padding16, style.innerMarginTop]}>
                        <View style={[style.flex, style.alignCenter]}>
                            <Thumbnail style={style.secondaryBGColor} large source={{uri: this.state.user.picture}}/>
                            <Text style={[style.primaryTextColor, style.fontFamily]}>{this.state.user.username}</Text>
                        </View>
                        <View
                            style={[style.secondaryBGColor, style.padding16, style.marginTop16, style.marginBottom16]}>
                            <Item>
                                <Input placeholder='İsim'
                                       value={this.state.name}
                                       onChangeText={this.setName.bind(this)}
                                       style={[style.primaryTextColor, style.fontFamily]}/>
                            </Item>
                            <Item>
                                <Input placeholder='Soyisim'
                                       value={this.state.surname}
                                       onChangeText={this.setSurname.bind(this)}
                                       style={[style.primaryTextColor, style.fontFamily]}/>
                            </Item>
                            <Item>
                                <Input keyboardType='numeric' placeholder='Telefon: 0*********'
                                       value={this.state.amount}
                                       maxLength={11}
                                       onChangeText={this.setPhone.bind(this)}
                                       style={[style.primaryTextColor, style.fontFamily]}/>
                            </Item>
                            <View style={[style.flex, style.marginTop16]}>
                                <Text note style={style.fontFamily}>Doğum Tarihi</Text>
                                <View style={style.knockOutInnerContainer}>
                                    <Picker
                                        mode="dropdown"
                                        placeholder="Gün"
                                        style={[style.primaryTextColor, style.fontFamily]}
                                        selectedValue={this.state.bDay}
                                        onValueChange={this.onDayChange.bind(this)}
                                    >
                                        {dayPickers}
                                    </Picker>
                                    <Picker
                                        mode="dropdown"
                                        placeholder="Ay"
                                        style={[style.primaryTextColor, style.fontFamily]}
                                        selectedValue={this.state.bMonth}
                                        onValueChange={this.onMonthChange.bind(this)}
                                    >
                                        {monthPickers}
                                    </Picker>
                                    <Picker
                                        mode="dropdown"
                                        placeholder="Yıl"
                                        style={[style.primaryTextColor, style.fontFamily]}
                                        selectedValue={this.state.bYear}
                                        onValueChange={this.onYearChange.bind(this)}
                                    >
                                        {yearPickers}
                                    </Picker>
                                </View>
                            </View>
                        </View>

                        <Button rounded block style={style.customBGColor}
                                onPress={this.confirm.bind(this)}
                                disabled={this.state.loading}>
                            <Text style={style.fontFamily}>ONAYLA</Text>
                        </Button>
                    </View>
                </ScrollView>
            </Root>
        );
    }
}