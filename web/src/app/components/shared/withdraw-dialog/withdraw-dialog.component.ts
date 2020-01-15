import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { REGEX } from '../../../constants/regex.constant';
import {Router} from "@angular/router";

@Component({
  selector: 'app-withdraw-dialog',
  templateUrl: './withdraw-dialog.component.html',
  styleUrls: ['./withdraw-dialog.component.scss']
})
export class WithdrawDialogComponent implements OnInit {

    withdrawForm = new FormGroup({
        withdrawalAmount: new FormControl(),
        iban: new FormControl()
    });

    constructor(public dialog: MatDialogRef<WithdrawDialogComponent>, private router: Router,
                @Inject(MAT_DIALOG_DATA) public params: {budget: number, iban: string}) { }

    ngOnInit() {
        if (this.params.budget >= 50) {
            this.withdrawForm.controls.withdrawalAmount.setValidators([Validators.required, Validators.min(50),
                Validators.max(this.params.budget), Validators.pattern(REGEX.DECIMAL_NUMBER)]);
        } else {
            this.withdrawForm.controls.withdrawalAmount.disable();
        }

        if (this.params.iban) {
            this.withdrawForm.controls.iban.setValue(this.params.iban);
        }
        this.withdrawForm.controls.iban.disable();
    }

    openSettingsPage() {
        this.router.navigateByUrl('app/settings');
        this.dialog.close();
    }

    withdraw() {
        const controls = this.withdrawForm.controls;
        if (this.withdrawForm.valid) {
            this.dialog.close({
                withdrawalAmount: controls.withdrawalAmount.value,
                iban: this.params.iban
            });
        }
    }

}
