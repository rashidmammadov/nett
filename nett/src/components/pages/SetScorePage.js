import React, {Component} from 'react';
import {ScrollView, View, TouchableOpacity, BackHandler} from 'react-native';
import {Badge, Body, Button, Header, Input, Left, List, ListItem, Right, Text, Thumbnail, Title} from 'native-base';
import {Actions} from "react-native-router-flux";
import Icon from 'react-native-vector-icons/Feather';
import {style} from "../../assets/style/Custom";
import LoadingDialog from "../LoadingDialog";
import Dialog, {DialogContent} from "react-native-popup-dialog";
import {setMatchScore} from '../../services/FixtureService';
import {SUCCESS} from "../../services/Constants";
import {googleTrack} from "../../services/GoogleAnalytics";
import {errorToast, successToast, warningToast} from "../../services/ToastService";

export default class SetScorePage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: false,
            approveDialog: false,
            tournamentId: this.props.tournamentId,
            tournamentType: this.props.tournamentType,
            match: this.props.match,
            deactiveButton: !this.props.match.available,
            homePoint: this.props.match.home.point ? this.props.match.home.point.toString() : '0',
            awayPoint: this.props.match.away.point ? this.props.match.away.point.toString() : '0'
        };

        this.handleBackButtonClick = this.handleBackButtonClick.bind(this);
    }

    componentDidMount() {
        BackHandler.addEventListener('hardwareBackPress', this.handleBackButtonClick);
    }

    componentWillUnmount() {
        BackHandler.removeEventListener('hardwareBackPress', this.handleBackButtonClick);
    }

    handleBackButtonClick() {
        Actions.pop();
        return true;
    }

    setHomePoint(value) {
        this.setState({homePoint: value});
    }

    setAwayPoint(value) {
        this.setState({awayPoint: value});
    }

    setScore() {
        let data = {
            tournamentId: this.state.tournamentId,
            tournamentType: this.state.tournamentType,
            tourId: this.state.match.tourId,
            matchId: this.state.match.matchId,
            homePoint: Number(this.state.homePoint),
            awayPoint: Number(this.state.awayPoint)
        };
        this.sendRequestToUpdateScore(data);
    }

    sendRequestToUpdateScore(data) {
        let result = {};
        googleTrack('Set Score Page', 'send request to update score', data);
        this.setState({approveDialog: false, loading: true});
        setMatchScore(data)
            .then((res) => {
                this.setState({loading: false});
                if (res.status === SUCCESS) {
                    result = res.data;
                    this.props.setMatchScore(result);
                    successToast(res.message);
                    this.setState({deactiveButton: true});
                    googleTrack('Set Score Page', 'response of set score', res.message);
                } else {
                    warningToast(res.message);
                    googleTrack('Set Score Page', 'warning of set score', res.message);
                }
            })
            .catch((error) => {
                this.   setState({loading: false});
                errorToast(error.message);
                googleTrack('Set Score Page', 'error of set score', error.message);
            });
    }

    render() {
        return (
            <ScrollView style={style.primaryBGColor}>
                <LoadingDialog loading={this.state.loading}/>

                <Header style={style.secondaryBGColor}>
                    <Left>
                        <TouchableOpacity transparent onPress={() => this.handleBackButtonClick()}>
                            <Icon name='arrow-left' color={'#f8f8f8'} size={24}/>
                        </TouchableOpacity>
                    </Left>
                    <Body>
                    <Title style={[style.primaryTextColor, style.fontFamily]}>{this.props.title}</Title>
                    </Body>
                </Header>
                <List>
                    <ListItem style={style.knockOutContainer}>
                        <Body style={style.knockOutInnerContainer}>
                        <Left style={[style.flex, style.alignColumn, style.alignCenter]}>
                            <Thumbnail style={style.secondaryBGColor} source={{uri: this.state.match.home.picture}}/>
                            <Text style={[style.flex, style.fontFamily, style.primaryTextColor]} numberOfLines={1}>
                                {this.state.match.home.username}
                            </Text>
                            <View style={[style.width64, style.alignCenter]}>
                                <Input keyboardType='numeric'
                                       value={this.state.homePoint}
                                       onChangeText={this.setHomePoint.bind(this)}
                                       style={[style.primaryTextColor, style.fontFamily, style.largeFont]}/>
                            </View>
                        </Left>
                        <Badge style={[style.imageBGColor]}>
                            <Text style={[style.fontFamily, style.secondaryTextColor, style.boldFont, style.smallFont]}>
                                {this.state.match.date}
                            </Text>
                        </Badge>
                        <Right style={[style.flex, style.alignColumn, style.alignCenter]}>
                            <Thumbnail style={style.secondaryBGColor} source={{uri: this.state.match.away.picture}}/>
                            <Text style={[style.flex, style.fontFamily, style.primaryTextColor]} numberOfLines={1}>
                                {this.state.match.away.username}
                            </Text>
                            <View style={[style.width64, style.alignCenter]}>
                                <Input keyboardType='numeric'
                                       value={this.state.awayPoint}
                                       onChangeText={this.setAwayPoint.bind(this)}
                                       style={[style.primaryTextColor, style.fontFamily, style.largeFont]}/>
                            </View>
                        </Right>
                        </Body>
                    </ListItem>

                    <View style={[style.margin16, style.alignColumn]}>
                        <Button block rounded disabled={this.state.deactiveButton}
                                style={this.state.deactiveButton ? style.disabledBGColor : style.customBGColor}
                                onPress={() => this.setState({approveDialog: true})}>
                            <Text style={[style.primaryTextColor, style.fontFamily]}>Devam</Text>
                        </Button>
                        <Text note style={[style.fontFamily, style.marginTop16]}>* Kullanıcı adı altındaki rakamlara tıklayarak skoru girebilirsin.</Text>
                        <Text note style={[style.fontFamily]}>* Maç sonucu berabere bitemez. (Uzatmalar, Penaltılar vs. ile kazanan belirlenmelidir)</Text>
                        <Text note style={[style.fontFamily]}>* Maç sonucu sadece bir kere girilebilir.</Text>
                        <Text note style={[style.fontFamily]}>* Maça gelmeyen veya kural dışı haraket yapan taraf için teknik yenilgi girebilirsin. (örnek: 3-0, 0-3)</Text>
                        <Text note style={[style.fontFamily]}>* Kazanan taraf bir sonraki turdaki rakipiyle otomatik olarak eşleşecektir.</Text>
                        <Text note style={[style.fontFamily]}>* Sıralama ve Ödüller final maçından sonra katılımcı sayısına bağlı olarak otomatik hesaplanacaktır.</Text>
                        <Dialog visible={this.state.approveDialog} onTouchOutside={() => {this.setState({approveDialog: false});}}>
                            <DialogContent style={{margin: 16}}>
                                <Text style={[style.secondaryTextColor, style.fontFamily, style.bold]}>
                                    Güvenlik açısından bu skoru onayladıktan sonra skorda değişilik yapamazsın.
                                </Text>
                                <View style={[style.alignRow, style.marginTop16, style.spaceBetween]}>
                                    <Button small rounded bordered onPress={() => this.setState({approveDialog: false})}>
                                        <Text uppercase={false} style={[style.customColor, style.fontFamily]}>İptal Et</Text>
                                    </Button>
                                    <Button small rounded style={style.customBGColor} onPress={() => this.setScore()}>
                                        <Text uppercase={false} style={[style.primaryTextColor, style.fontFamily]}>Onayla</Text>
                                    </Button>
                                </View>
                            </DialogContent>
                        </Dialog>
                    </View>
                </List>
            </ScrollView>
        );
    }
}