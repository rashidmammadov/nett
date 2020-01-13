import { Component, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { UserType } from '../../interfaces/user-type';
import { DepositDialogComponent } from '../shared/deposit-dialog/deposit-dialog.component';
import { FinanceArchiveDialogComponent } from '../shared/finance-archive-dialog/finance-archive-dialog.component';
import { WithdrawDialogComponent } from '../shared/withdraw-dialog/withdraw-dialog.component';
import { FinanceService } from '../../services/finance/finance.service';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { ToastService } from '../../services/toast/toast.service';
import { loaded, loading } from '../../store/actions/progress.action';
import { set } from '../../store/actions/user.action';

@Component({
  selector: 'app-bank',
  templateUrl: './bank.component.html',
  styleUrls: ['./bank.component.scss']
})
export class BankComponent implements OnInit {

    public user: UserType;
    threeDSResult: string;

    constructor(private store: Store<{user: UserType, progress: boolean}>, private dialog: MatDialog,
                private financeService: FinanceService) { }

    async ngOnInit() {
        await this.getUserData();
    }

    openDepositDialog(): void {
        this.dialog
            .open(DepositDialogComponent, { width: '500px', disableClose: true })
            .afterClosed().toPromise()
            .then((result) => !!result && this.threeDS(result) );
    }

    openFinanceArchiveDialog(): void {
        this.dialog
            .open(FinanceArchiveDialogComponent, { width: '500px', disableClose: true });
    }

    openWithdrawDialog(): void {
        this.dialog
            .open(WithdrawDialogComponent, { width: '500px', disableClose: true,
                data: { budget: this.user.budget, iban: this.user.iban }})
            .afterClosed().toPromise()
            .then((result) => !!result && this.withdraw(result) );
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    };

    private deposit = async (params) => {
      console.log(params);
        // this.store.dispatch(loading());
        // const result = await this.financeService.deposit(params);
        // UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            // this.user.budget = response.data.budget;
            // this.store.dispatch(set({user: this.user}));
            // ToastService.show(response.message);
        // });
        // this.store.dispatch(loaded());
    };

    private threeDS = async (params) => {
        this.store.dispatch(loading());
        const result = await this.financeService.deposit(params);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.threeDSResult = response.data;
        });
        this.store.dispatch(loaded());
    };

    private withdraw = async (params) => {
        this.store.dispatch(loading());
        const result = await this.financeService.withdraw(params);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.user.budget = response.data.budget;
            this.store.dispatch(set({user: this.user}));
            ToastService.show(response.message);
        });
        this.store.dispatch(loaded());
    };

}
