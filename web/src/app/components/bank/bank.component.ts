import { Component, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { UserType } from '../../interfaces/user-type';

@Component({
  selector: 'app-bank',
  templateUrl: './bank.component.html',
  styleUrls: ['./bank.component.scss']
})
export class BankComponent implements OnInit {

    public user: UserType;

    constructor(private store: Store<{user: UserType}>) { }

    async ngOnInit() {
        await this.getUserData();
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    };

}
