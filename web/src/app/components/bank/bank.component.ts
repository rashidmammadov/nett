import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { UserType } from '../../interfaces/user-type';
import { FinanceArchiveDialogComponent } from '../shared/finance-archive-dialog/finance-archive-dialog.component';

@Component({
  selector: 'app-bank',
  templateUrl: './bank.component.html',
  styleUrls: ['./bank.component.scss']
})
export class BankComponent implements OnInit {

    public user: UserType;

    constructor(private store: Store<{user: UserType}>, private dialog: MatDialog) { }

    async ngOnInit() {
        await this.getUserData();
    }

    openFinanceArchiveDialog(): void {
        this.dialog.open(FinanceArchiveDialogComponent, {
            width: '500px'
        });
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    };

}
