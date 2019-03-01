import React from 'react'
import {Grid, LineChart, XAxis, YAxis} from 'react-native-svg-charts'
import {View} from 'react-native'

export default class StandingsChart extends React.PureComponent {

    constructor(props) {
        super(props);
    }

    render() {
        const axesSvg = { fontSize: 10, fill: '#d3d3d3' };
        const verticalContentInset = { top: 8, bottom: 8 };
        const xAxisHeight = 24;

        return (
            <View style={{ height: 200, padding: 16, flexDirection: 'row', backgroundColor: '#303030' }}>
                <YAxis
                    data={this.props.data}
                    style={{ marginBottom: xAxisHeight}}
                    formatLabel={(value) => -value}
                    contentInset={verticalContentInset}
                    svg={axesSvg}
                />
                <View style={{ flex: 1, marginLeft: 8 }}>
                    <LineChart
                        style={{ flex: 1 }}
                        data={this.props.data}
                        contentInset={verticalContentInset}
                        svg={{ stroke: '#f8f8f8', strokeWidth: 2 }}
                    >
                        <Grid/>
                    </LineChart>
                    <XAxis
                        style={{ marginHorizontal: -8, height: xAxisHeight}}
                        data={this.props.data}
                        formatLabel={(value, index) => this.props.data.length - index}
                        contentInset={{ left: 8, right: 8 }}
                        svg={axesSvg}
                    />
                </View>
            </View>
        )
    }

}