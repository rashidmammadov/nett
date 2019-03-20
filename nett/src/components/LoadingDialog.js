import React, { Component } from 'react';
import {Dialog} from "react-native-popup-dialog";
import {Spinner} from "native-base";

export default class LoadingDialog extends Component {

    constructor(props) {
        super(props);
    }

    render() {
        return (
            <Dialog visible={this.props.loading}>
                <Spinner color='#7F00FF' style={{marginLeft: 24, marginRight: 24}} />
            </Dialog>
        );
    }
}