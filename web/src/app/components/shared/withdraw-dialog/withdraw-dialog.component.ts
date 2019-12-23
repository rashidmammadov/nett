import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';
import { FormControl, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-withdraw-dialog',
  templateUrl: './withdraw-dialog.component.html',
  styleUrls: ['./withdraw-dialog.component.scss']
})
export class WithdrawDialogComponent implements OnInit {

    withdrawForm = new FormGroup({
        withdrawalAmount: new FormControl(null, [Validators.required, Validators.min(50)]),
        iban: new FormControl('', [Validators.required])
    });

    constructor(public dialog: MatDialogRef<WithdrawDialogComponent>,
                @Inject(MAT_DIALOG_DATA) public params: {budget: number, iban: string}) { }

    ngOnInit() {
        if (this.params.budget >= 50) {
            this.withdrawForm.controls.withdrawalAmount.setValidators([Validators.required, Validators.min(50),
                Validators.max(this.params.budget)]);
        } else {
            this.withdrawForm.controls.withdrawalAmount.disable();
            this.withdrawForm.controls.iban.disable();
        }

        if (this.params.iban) {
            this.withdrawForm.controls.iban.setValue(this.params.iban);
        }
    }

    withdraw() {
        const controls = this.withdrawForm.controls;
        this.dialog.close({
            withdrawalAmount: controls.withdrawalAmount.value,
            iban: controls.iban.value
        });
    }

}
