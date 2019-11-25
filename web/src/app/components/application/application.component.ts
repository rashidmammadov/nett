import { Component } from '@angular/core';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { UserType } from '../../interfaces/user-type';
import { TYPES } from '../../constants/types.constant';

const HOME = {link: 'home', icon: 'home'};
const BELONG = {link: 'belongs', icon: 'grid'};
const SEARCH = {link: 'search', icon: 'search'};
const SET_TOURNAMENT = {link: 'set-tournament', icon: 'plus-square'};
const BALANCE = {link: 'balance', icon: 'credit-card'};
const SETTINGS = {link: 'settings', icon: 'user'};
@Component({
  selector: 'app-application',
  templateUrl: './application.component.html',
  styleUrls: ['./application.component.scss']
})
export class ApplicationComponent {
    tabs: object[] = [];
    user: UserType;

    constructor(private store: Store<{user: UserType}>) {
        this.getUserData().then();
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
        this.prepareTabs(this.user.type);
    };

    private prepareTabs(userType: string) {
        if (userType === TYPES.USER.HOLDER) {
            this.tabs = [HOME, BELONG, SEARCH, SET_TOURNAMENT, SETTINGS];
        } else if (userType === TYPES.USER.PLAYER) {
            this.tabs = [HOME, BELONG, SEARCH, BALANCE, SETTINGS];
        }
    }
}
