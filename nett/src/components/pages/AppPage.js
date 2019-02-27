import React, { Component } from 'react';
import {Root, Container, Header, Content, Footer, FooterTab, Button, Text, Body, Title} from 'native-base';
import DashboardPage from './DashboardPage.js';
import Icon from 'react-native-vector-icons/Feather';
import {style} from '../../assets/style/Custom.js';

const tabPages = [<DashboardPage />, <DashboardPage />, <DashboardPage />, <DashboardPage />, <DashboardPage />];

export default class AppPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            activeTab: 0
        }
        this.changeTab.bind(this);
    }

    changeTab(tab) {
        this.setState({
            activeTab: tab
        })
    }

	render() {
		return (
		    <Root>
                <Container>
                    <Header noLeft>
                        <Body>
                            <Title>{this.props.title}</Title>
                        </Body>
                    </Header>
                    {tabPages[this.state.activeTab]}
                    <Footer>
                        <FooterTab style={style.footer}>
                            <Button onPress={() => this.changeTab(0)}
                                style={this.state.activeTab === 0 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 0 ? true : false}>
                                <Icon name="home" size={24}
                                    style={this.state.activeTab === 0 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                            <Button onPress={() => this.changeTab(1)}
                                style={this.state.activeTab === 1 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 1 ? true : false}>
                                <Icon name="grid" size={24}
                                    style={this.state.activeTab === 1 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                            <Button onPress={() => this.changeTab(2)}
                                style={this.state.activeTab === 2 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 2 ? true : false}>
                                <Icon name="plus-square" size={24}
                                    style={this.state.activeTab === 2 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                            <Button onPress={() => this.changeTab(3)}
                                style={this.state.activeTab === 3 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 3 ? true : false}>
                                <Icon name="credit-card" size={24}
                                    style={this.state.activeTab === 3 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                            <Button onPress={() => this.changeTab(4)}
                                style={this.state.activeTab === 4 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 4 ? true : false}>
                                <Icon name="user" size={24}
                                    style={this.state.activeTab === 4 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                        </FooterTab>
                    </Footer>
                </Container>
            </Root>
		);
	}
}