import {GoogleAnalyticsTracker} from "react-native-google-analytics-bridge";
import {GOOGLE_ANALYTICS_ID} from './Constants';
import {user} from "./ConfigService";

let tracker = new GoogleAnalyticsTracker(GOOGLE_ANALYTICS_ID);
tracker.setAppName('nett app');

export let googleTrack = (screenName, action, data) => {
    let category = user ? user.username : 'anonymous';
    let label = data !== null ? JSON.stringify(data) : null;
    tracker.trackScreenView(screenName);
    tracker.trackEvent(category, action, {label: label, value: 0});
};