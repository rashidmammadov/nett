import React, {Component} from 'react';
import {View} from 'react-native';
import {Text, Item, Input, Button, Toast} from 'native-base';
import Icon from 'react-native-vector-icons/Feather';

export default class DepositTab extends Component {

    constructor(props) {
        super(props);
        this.state = {
            amount: '',
            cardHolder: '',
            cardNumber: '',
            lastMonth: '',
            lastYear: '',
            cvv: '',
            amountStatus: false,
            cardHolderStatus: false,
            cardNumberStatus: false,
            lastMonthStatus: false,
            lastYearStatus: false,
            cvvStatus: false,
        };
    }

    setAmount(value) {
        this.setState({
            amount: value,
            amountStatus: Number(value) >= 15
        })
    }

    setCardHolder(value) {
        this.setState({
            cardHolder: value,
            cardHolderStatus: value.length > 2
        });
    }

    setCardNumber(value) {
        this.setState({
            cardNumber: value,
            cardNumberStatus: value.length === 16
        });
    }

    setLastMonth(value) {
        this.setState({
            lastMonth: value,
            lastMonthStatus: Number(value) >= 1 && Number(value) <= 12
        });
    }

    setLastYear(value) {
        this.setState({
            lastYear: value,
            lastYearStatus: Number(value) >= new Date().getFullYear().toString().substr(-2) &&
                value.length === 2
        });
    }

    setCvv(value) {
        this.setState({
           cvv: value,
           cvvStatus: value.length === 3
        });
    }

    doDeposit() {
        if (this.state.amountStatus && this.state.cardHolderStatus && this.state.cardNumberStatus &&
            this.state.lastMonthStatus && this.state.lastYearStatus && this.state.cvvStatus) {

        } else {
            Toast.show({
                text: 'Eksik veya hatalı bilgi',
                buttonText: 'tamam',
                type:  'warning'
            });
        }
    }

    render() {
        let amountIcon, cardHolderIcon, cardNumberIcon, lastMonthIcon, lastYearIcon, cvvIcon;
        let successIcon = <Icon name='check' size={16} color={'green'} />;

        if (this.state.amountStatus) { amountIcon = successIcon; }
        if (this.state.cardHolderStatus) { cardHolderIcon = successIcon; }
        if (this.state.cardNumberStatus) { cardNumberIcon = successIcon; }
        if (this.state.lastMonthStatus) { lastMonthIcon = successIcon; }
        if (this.state.lastYearStatus) { lastYearIcon = successIcon; }
        if (this.state.cvvStatus) { cvvIcon = successIcon; }

        return (
            <View style={{padding: 16, backgroundColor: '#303030'}}>
                <Item success={this.state.amountStatus}>
                    <Input keyboardType='numeric' placeholder='Miktar min: 15 ₺'
                           value={this.state.amount}
                           onChangeText={this.setAmount.bind(this)}
                           style={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} />
                    {amountIcon}
                </Item>

                <Item success={this.state.cardHolderStatus}>
                    <Input placeholder='Kart Üzerindeki Ad, Soyad'
                           value={this.state.cardHolder}
                           onChangeText={this.setCardHolder.bind(this)}
                           style={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} />
                    {cardHolderIcon}
                </Item>

                <Item success={this.state.cardNumberStatus}>
                    <Input keyboardType='numeric' placeholder='Kart Numarası'
                           value={this.state.cardNumber}
                           onChangeText={this.setCardNumber.bind(this)}
                           style={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} />
                    {cardNumberIcon}
                </Item>

                <View style={{flex: 1, flexDirection: 'row'}}>
                    <Item style={{flex: 1}} success={this.state.lastMonthStatus}>
                        <Input keyboardType='numeric' placeholder='Ay'
                               value={this.state.lastMonth}
                               onChangeText={this.setLastMonth.bind(this)}
                               style={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} />
                        {lastMonthIcon}
                    </Item>

                    <Item style={{flex: 1}} success={this.state.lastYearStatus}>
                        <Input keyboardType='numeric' placeholder='Yıl'
                               value={this.state.lastYear}
                               onChangeText={this.setLastYear.bind(this)}
                               style={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} />
                        {lastYearIcon}
                    </Item>

                    <Item style={{flex: 1, marginLeft: 32}} success={this.state.cvvStatus}>
                        <Input keyboardType='numeric' placeholder='CVV'
                               value={this.state.cvv}
                               onChangeText={this.setCvv.bind(this)}
                               style={{color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} />
                        {cvvIcon}
                    </Item>
                </View>

                <Button rounded block style={{backgroundColor: '#7F00FF', marginTop: 32}} onPress={this.doDeposit.bind(this)}>
                    <Text style={{fontFamily: 'GoogleSans-Regular'}}>ÖDEME YAP</Text>
                </Button>
            </View>
        );
    }
}