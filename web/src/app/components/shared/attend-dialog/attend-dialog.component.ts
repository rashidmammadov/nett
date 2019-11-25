import { Component, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material';
import { UserType } from '../../../interfaces/user-type';
import { TYPES } from 'src/app/constants/types.constant';

@Component({
  selector: 'app-attend-dialog',
  templateUrl: './attend-dialog.component.html',
  styleUrls: ['./attend-dialog.component.scss']
})
export class AttendDialogComponent {

    constructor(public dialog: MatDialogRef<AttendDialogComponent>, @Inject(MAT_DIALOG_DATA) public user: UserType) {}

    attend(paymentType: string) {
        this.dialog.close(TYPES.PAYMENT[paymentType]);
    }

}
