import React, {Component} from 'react';
import {ScrollView, View, TouchableOpacity, BackHandler} from 'react-native';
import {Badge, Body, Button, Header, Input, Left, List, ListItem, Right, Text, Thumbnail, Title} from 'native-base';
import {Actions} from "react-native-router-flux";
import Icon from 'react-native-vector-icons/Feather';
import {style} from "../../assets/style/Custom";
import LoadingDialog from "../LoadingDialog";

export default class SetScorePage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            loading: false,
            match: this.props.match,
            homePoint: this.props.match.home.point || '0',
            awayPoint: this.props.match.away.point || '0'
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
        let match = Object.assign({}, this.props.match);
        match.home.point = this.state.homePoint;
        match.away.point = this.state.awayPoint;
        this.props.setMatchScore(match);
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
                        <Badge style={style.knockOutBadge}>
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

                    <Button small rounded style={style.customBGColor} onPress={() => this.setScore()}>
                        <Text uppercase={false} style={{color: '#f0f0f0', fontFamily: 'GoogleSans-Regular'}}>Hesabımdan</Text>
                    </Button>
                </List>
            </ScrollView>
        );
    }
}