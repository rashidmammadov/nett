import React, {Component} from 'react';
import {ScrollView, View, TouchableOpacity} from 'react-native';
import {List, ListItem, Badge, Text, Left, Body, Right} from 'native-base';
import {style} from '../../../assets/style/Custom.js';
import {UNDEFINED_USERNAME, VERSUS} from '../../../services/Constants';
import {Actions} from "react-native-router-flux";

export default class KnockOutFixture extends Component {

    constructor(props) {
        super(props);
        this.state = {
            visible: false
        };
    }

    render() {
        let matches = (data) => {
            let view = data.map(function (match, i) {
                if (match.home || match.away) {
                    return <ListItem key={i} style={style.knockOutContainer}>
                        <Body style={style.knockOutInnerContainer}>
                        <Left style={style.flex}>
                            <Text style={[style.flex, style.fontFamily, style.primaryTextColor]} numberOfLines={1}>
                                {match.home && match.home.username ? match.home.username : UNDEFINED_USERNAME}
                            </Text>
                        </Left>
                        <TouchableOpacity onPress={() => {
                            Actions.SetScorePage({match: match})
                        }}>
                            <Badge style={style.knockOutBadge}>
                                <Text
                                    style={[style.fontFamily, style.secondaryTextColor, style.boldFont, style.smallFont]}>
                                    {match.home && match.home.point !== null && match.away && match.away.point !== null ?
                                        (match.home.point + ' - ' + match.away.point) : VERSUS}
                                </Text>
                            </Badge>
                        </TouchableOpacity>
                        <Right style={style.flex}>
                            <Text style={[style.flex, style.fontFamily, style.primaryTextColor]} numberOfLines={1}>
                                {match.away && match.away.username ? match.away.username : UNDEFINED_USERNAME}
                            </Text>
                        </Right>
                        </Body>
                    </ListItem>;
                }
            });
            return view;
        };

        let rounds = this.props.fixture && this.props.fixture.draws.map((draw, i) => {
            let view = <View key={i}>
                <ListItem style={style.secondaryBGColor} itemDivider>
                    <Left>
                        <Text style={[style.fontFamily, style.primaryTextColor]}>{draw.drawTitle}</Text>
                    </Left>
                    <Right style={style.flex}>
                        <Text note style={[style.flex, style.fontFamily]}>{draw.matches[0].date}</Text>
                    </Right>
                </ListItem>
                {matches(draw.matches)}
            </View>;
            return view
        });

        if (this.props.fixture) {
            return (
                <ScrollView>
                    <List>
                        {rounds}
                    </List>
                </ScrollView>
            );
        } else {
            return <View style={style.columnCenter}>
                <Text style={[style.fontFamily, style.primaryTextColor]}>Fikstür bilgileri bulunamadı</Text>
            </View>
        }

    }
}