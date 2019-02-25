import React, { Component } from 'react';
import {Root, Container, Header, Title, Content, Button, Icon, Left, Right, Body, Text} from 'native-base';

export default class DashboardPage extends Component {
	render() {
		return (
		    <Root>
                <Container>
                    <Header noLeft>
                        <Left>
                            <Button transparent>
                                <Icon name="arrow-back" />
                            </Button>
                        </Left>
                        <Body>
                            <Title>{this.props.title}</Title>
                        </Body>
                        <Right>
                            <Button transparent>
                                <Text>Cancel</Text>
                            </Button>
                        </Right>
                    </Header>
                    <Content padder>
                        <Text>
                            Header with noLeft prop, eliminates Left component for Android
                        </Text>
                    </Content>
                </Container>
            </Root>
		);
	}
}