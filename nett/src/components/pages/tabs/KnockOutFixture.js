import React, {Component} from 'react';
import {ScrollView, View, TouchableOpacity} from 'react-native';
import {List, ListItem, Badge, Text, Left, Body, Right} from 'native-base';
import {style} from '../../../assets/style/Custom.js';
import {UNDEFINED_USERNAME, VERSUS} from '../../../services/Constants';

export default class KnockOutFixture extends Component {

    constructor(props) {
        super(props);
        this.state = {
            fixture: {
                "tournamentId": 8,
                "holderId": 1,
                "gameId": 2,
                "tournamentType": "knock_out",
                "participantCount": 16,
                "createdAt": "22/03/2019 14:19",
                "draws": [{
                    "drawTitle": "1/4",
                    "matches": [{
                        "available": false,
                        "home": {"id": 1, "username": "rashidmammadov", "point": 12},
                        "away": {"id": 2, "username": "cananozbaykal", "point": 1},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }, {
                        "available": false,
                        "home": {"id": 3, "username": "yasindongelli", "point": 0},
                        "away": {"id": 4, "username": "mammadosmanov", "point": 3},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }, {
                        "available": false,
                        "home": {"id": 5, "username": "nihattalibzade", "point": 5},
                        "away": {"id": 6, "username": "faridmammadov", "point": 2},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }, {
                        "available": false,
                        "home": {"id": 7, "username": "johndoe", "point": 0},
                        "away": {"id": 8, "username": "beatmeifyoucan", "point": 8},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }]
                }, {
                    "drawTitle": "1/2",
                    "matches": [{
                        "available": false,
                        "home": {"id": 1, "username": "rashidmammadov", "point": 4},
                        "away": {"id": 4, "username": "mammadosmanov", "point": 3},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }, {
                        "available": false,
                        "home": {"id": 5, "username": "nihattalibzade", "point": 2},
                        "away": {"id": 8, "username": "beatmeifyoucan", "point": 0},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }]
                }, {
                    "drawTitle": "3th",
                    "matches": [{
                        "available": false,
                        "home": {"id": 4, "username": "mammadosmanov", "point": null},
                        "away": {"id": 8, "username": "beatmeifyoucan", "point": null},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }]
                }, {
                    "drawTitle": "final",
                    "matches": [{
                        "available": false,
                        "home": {"id": 1, "username": "rashidmammadov", "point": null},
                        "away": {"id": 5, "username": "nihattalibzade", "point": null},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }]
                }]
            }
        };
    }

    render() {
        let matches = (data) => {
            let view = data.map(function (match, i) {
                return <ListItem key={i} style={style.knockOutContainer}>
                    <Body style={style.knockOutInnerContainer}>
                    <Left style={style.flex}>
                        <Text style={[style.flex, style.fontFamily, style.primaryTextColor]} numberOfLines={1}>
                            {match.home.username ? match.home.username : UNDEFINED_USERNAME}
                        </Text>
                    </Left>
                    <Badge style={style.knockOutBadge}>
                        <Text style={[style.fontFamily, style.secondaryTextColor, style.boldFont, style.smallFont]}>
                            {match.home.point !== null && match.away.point !== null ? (match.home.point + ' - ' + match.away.point) : VERSUS}
                        </Text>
                    </Badge>
                    <Right style={style.flex}>
                        <Text style={[style.flex, style.fontFamily, style.primaryTextColor]} numberOfLines={1}>
                            {match.away.username ? match.away.username : UNDEFINED_USERNAME}
                        </Text>
                    </Right>
                    </Body>
                </ListItem>;
            });
            return view;
        };

        let rounds = this.state.fixture.draws.map((draw, i) => {
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

        return (
            <ScrollView>
                <List>
                    {rounds}
                </List>
            </ScrollView>
        );
    }
}