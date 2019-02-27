import React, { Component } from 'react';
import {View, Text} from 'react-native';

export default class DashboardPage extends Component {
	render() {
		return (
		    <View
                style={{
                    flex: 1,
                    flexDirection: 'column',
                    alignItems: 'center',
                    justifyContent: 'center'
                }}>
                <Text>DashboardPage</Text>
            </View>
		);
	}
}