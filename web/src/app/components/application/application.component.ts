import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-application',
  templateUrl: './application.component.html',
  styleUrls: ['./application.component.scss']
})
export class ApplicationComponent {
    tabs: object[];

    constructor() {
        this.tabs = [
            {link: 'home', icon: 'home'},
            {link: 'belongs', icon: 'grid'},
            {link: 'search', icon: 'search'},
            {link: 'set-tournament', icon: 'plus-square'},
            {link: 'balance', icon: 'credit-card'},
            {link: 'settings', icon: 'user'},
        ]
    }

}
