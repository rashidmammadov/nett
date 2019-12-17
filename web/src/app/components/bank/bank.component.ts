import { Component, OnInit } from '@angular/core';
import { MatBottomSheet } from '@angular/material/bottom-sheet';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { UserType } from '../../interfaces/user-type';
import { FinanceArchiveSheetComponent } from '../shared/finance-archive-sheet/finance-archive-sheet.component';

@Component({
  selector: 'app-bank',
  templateUrl: './bank.component.html',
  styleUrls: ['./bank.component.scss']
})
export class BankComponent implements OnInit {

    public user: UserType;

    constructor(private store: Store<{user: UserType}>, private bottomSheet: MatBottomSheet) { }

    async ngOnInit() {
        await this.getUserData();
    }

    openFinanceArchiveSheet(): void {
        this.bottomSheet.open(FinanceArchiveSheetComponent);
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    };

}
