import React, { Component } from 'react';
import { Image } from 'react-native';
import { Container, Header, Content, Card, CardItem, Thumbnail, Text, Button, Icon, Left, Body, Right } from 'native-base';

export default class TournamentCard extends Component {

    render() {
        return (
            <Card>
                <CardItem>
                    <Left>
                        <Thumbnail source={this.props.tournament.image} />
                        <Body>
                              <Text>{this.props.tournament.holder}</Text>
                              <Text note>Izmir/Bornova</Text>
                        </Body>
                    </Left>
                </CardItem>
            </Card>
        );
    }
}