import React, { Component } from 'react';
import {Root, Container, Header, Footer, FooterTab, Button, Body, Title} from 'native-base';
import BudgetPage from './BudgetPage.js';
import DashboardPage from './DashboardPage.js';
import MyTournamentsPage from './MyTournamentsPage.js';
import SearchPage from './SearchPage.js';
import SetTournamentPage from './SetTournamentPage.js';
import Icon from 'react-native-vector-icons/Feather';
import {style} from '../../assets/style/Custom.js';

const tabPages = [<DashboardPage />, <MyTournamentsPage />, <SearchPage />, <SetTournamentPage />, <BudgetPage />, <DashboardPage />];

export default class AppPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            activeTab: 0
        };
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
                    <Header noLeft style={{backgroundColor: '#303030'}}>
                        <Body>
                            <Title style={{color: '#f8f8f8', fontSize: 20, fontFamily: 'GoogleSans-Regular'}}>{this.props.title}</Title>
                        </Body>
                    </Header>
                    <Container style={{backgroundColor: '#000'}}>
                        {tabPages[this.state.activeTab]}
                    </Container>
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
                                <Icon name="search" size={24}
                                      style={this.state.activeTab === 2 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                            <Button onPress={() => this.changeTab(3)}
                                style={this.state.activeTab === 3 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 3 ? true : false}>
                                <Icon name="plus-square" size={24}
                                    style={this.state.activeTab === 3 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                            <Button onPress={() => this.changeTab(4)}
                                style={this.state.activeTab === 4 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 4 ? true : false}>
                                <Icon name="credit-card" size={24}
                                    style={this.state.activeTab === 4 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                            <Button onPress={() => this.changeTab(5)}
                                style={this.state.activeTab === 5 ? style.footerActiveTab : style.footerTab}
                                active={this.state.activeTab === 5 ? true : false}>
                                <Icon name="user" size={24}
                                    style={this.state.activeTab === 5 ? style.footerActiveIcon : style.footerIcon} />
                            </Button>
                        </FooterTab>
                    </Footer>
                </Container>
            </Root>
		);
	}
}