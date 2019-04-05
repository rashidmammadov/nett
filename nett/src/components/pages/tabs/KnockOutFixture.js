import React, {Component} from 'react';
import {ScrollView, View, TouchableOpacity} from 'react-native';
import {List, ListItem, Badge, Text, Left, Body, Right} from 'native-base';

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
                        "available": true,
                        "home": {"id": 1, "username": "reshid", "point": 5},
                        "away": {"id": 2, "username": "rashidmammadov", "point": 1},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }, {
                        "available": false,
                        "home": {"id": 1, "username": "rashidmammadov", "point": 5},
                        "away": {"id": 2, "username": "cananozbaykal", "point": 1},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }, {
                        "available": false,
                        "home": {"id": 1, "username": "rashidmammadov", "point": 5},
                        "away": {"id": 2, "username": "cananozbaykal", "point": 1},
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
                        "home": {"id": 1, "username": "rashidmammadov", "point": 5},
                        "away": {"id": 2, "username": "cananozbaykal", "point": 1},
                        "homePoint": null,
                        "awayPoint": null,
                        "winner": null,
                        "loser": null,
                        "date": "12/04/2019 01:46",
                        "updatedAt": "22/03/2019 14:19"
                    }, {
                        "available": false,
                        "home": {"id": 1, "username": "rashidmammadov", "point": 5},
                        "away": {"id": 2, "username": "cananozbaykal", "point": 1},
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
                        "home": {"id": 1, "username": "rashidmammadov", "point": 5},
                        "away": {"id": 2, "username": "cananozbaykal", "point": 1},
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
                        "home": {"id": 1, "username": "rashidmammadov", "point": 5},
                        "away": {"id": 2, "username": "cananozbaykal", "point": 1},
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
                return <ListItem key={i} style={{paddingLeft: 0, paddingRight: 16}}>
                    <Body style={{
                        marginLeft: 0,
                        flex: 1,
                        flexDirection: 'row',
                        justifyContent: 'space-between',
                        alignItems: 'center'
                    }}>
                    <Left style={{flex: 1}}>
                        <Text style={{flex: 1, color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} numberOfLines={1}>
                            {match.home.username ? match.home.username : '---'}
                        </Text>
                    </Left>
                    <Badge style={{width: 72, backgroundColor: '#f8f8f8', marginLeft: 8, marginRight: 8}}>
                        <Text style={{fontSize: 14, color: '#303030', fontFamily: 'GoogleSans-Regular'}}>
                            {match.available ? (match.home.point + ' - ' + match.away.point) : 'vs'}
                        </Text>
                    </Badge>
                    <Right style={{flex: 1}}>
                        <Text style={{flex: 1, color: '#f8f8f8', fontFamily: 'GoogleSans-Regular'}} numberOfLines={1}>
                            {match.away.username ? match.away.username : '---'}
                        </Text>
                    </Right>
                    </Body>
                </ListItem>;
            });
            return view;
        };

        let rounds = this.state.fixture.draws.map((draw, i) => {
            let view = <View key={i}><ListItem style={{backgroundColor: '#303030'}} itemDivider>
                <Left>
                    <Text style={{fontFamily: 'GoogleSans-Regular', color: '#f8f8f8'}}>{draw.drawTitle}</Text>
                </Left>
                <Right style={{flex: 1}}>
                    <Text note style={{flex: 1, fontFamily: 'GoogleSans-Regular'}}>{draw.matches[0].date}</Text>
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