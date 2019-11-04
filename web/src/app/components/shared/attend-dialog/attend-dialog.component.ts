import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA } from '@angular/material';
import { UserType } from '../../../interfaces/user-type';

@Component({
  selector: 'app-attend-dialog',
  templateUrl: './attend-dialog.component.html',
  styleUrls: ['./attend-dialog.component.scss']
})
export class AttendDialogComponent implements OnInit {

    constructor(@Inject(MAT_DIALOG_DATA) public user: UserType) {
    }

    ngOnInit() {
    }

}
