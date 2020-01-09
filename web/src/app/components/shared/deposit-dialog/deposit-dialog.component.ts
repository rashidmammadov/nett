import { Component } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { MatDialogRef } from '@angular/material/dialog';
import { UtilityService } from '../../../services/utility/utility.service';
import { REGEX } from '../../../constants/regex.constant';

@Component({
  selector: 'app-deposit-dialog',
  templateUrl: './deposit-dialog.component.html',
  styleUrls: ['./deposit-dialog.component.scss']
})
export class DepositDialogComponent {
    availableYears: number[] = [];
    private currentDate = new Date();

    depositForm = new FormGroup({
        price: new FormControl('', [Validators.required, Validators.min(10)]),
        paidPrice: new FormControl('', [Validators.required, Validators.min(10)]),
        cardNumber: new FormControl('', [Validators.required, Validators.pattern(REGEX.CARD_NUMBER)]),
        cardHolderName: new FormControl('', [Validators.required]),
        expireMonth: new FormControl('', [Validators.required]),
        expireYear: new FormControl('', [Validators.required]),
        cvc: new FormControl('', [Validators.required])
    });

    constructor(public dialog: MatDialogRef<DepositDialogComponent>) {
        this.prepareDefaultValues();
    }

    deposit() {

    }

    private prepareDefaultValues() {
        let amount = UtilityService.calculateDepositCommission(10, 3.19);
        console.log(amount);
        const minYear = Number(this.currentDate.getFullYear().toString().slice(2, 4));
        const currentMonth = this.currentDate.getMonth() + 1;
        for (let i = minYear; i <= minYear + 15; i++) { this.availableYears.push(i); }
        this.depositForm.controls.expireMonth.setValue(currentMonth);
        this.depositForm.controls.expireYear.setValue(minYear);
    }

}
