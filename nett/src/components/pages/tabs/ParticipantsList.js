import React, {Component} from 'react';
import {Button} from 'react-native';
import {List, ListItem, Thumbnail, Text, Left, Body, Right} from 'native-base';
import Icon from 'react-native-vector-icons/Feather';

export default class ParticipantsList extends Component {

    constructor(props) {
        super(props);
        this.state = {
            data: this.props.participants
        };
    }

    render() {
        return (
            <List>
                <ListItem avatar>
                    <Left>
                        <Thumbnail small
                                   source={{uri: 'http://csfinance.org/wp-content/uploads/2015/03/Profile-Pic-Circle.jpg'}}/>
                    </Left>
                    <Body>
                    <Text style={{fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>rashidmammadov</Text>
                    <Text note style={{fontFamily: 'GoogleSans-Regular'}} numberOfLines={1}>Rashid Mammadov</Text>
                    </Body>
                    <Right>
                        <Text note style={{fontFamily: 'GoogleSans-Regular'}}>#1</Text>
                    </Right>
                </ListItem>
                <ListItem avatar>
                    <Left>
                        <Thumbnail small
                                   source={{uri: 'http://csfinance.org/wp-content/uploads/2015/03/Profile-Pic-Circle.jpg'}}/>
                    </Left>
                    <Body>
                    <Text style={{fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>cananozbaykal</Text>
                    <Text note style={{fontFamily: 'GoogleSans-Regular'}} numberOfLines={1}>Canan Özbaykal</Text>
                    </Body>
                    <Right>
                        <Text note style={{fontFamily: 'GoogleSans-Regular'}}>#2</Text>
                    </Right>
                </ListItem>
                <ListItem avatar>
                    <Left>
                        <Thumbnail small
                                   source={{uri: 'http://csfinance.org/wp-content/uploads/2015/03/Profile-Pic-Circle.jpg'}}/>
                    </Left>
                    <Body>
                    <Text style={{fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>johndoe</Text>
                    <Text note style={{fontFamily: 'GoogleSans-Regular'}} numberOfLines={1}>John Doe</Text>
                    </Body>
                    <Right>
                        <Text note style={{fontFamily: 'GoogleSans-Regular'}}>#24</Text>
                    </Right>
                </ListItem>
            </List>
        )
    }
}