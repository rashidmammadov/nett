import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

@Component({
  selector: 'app-set-match-score-dialog',
  templateUrl: './set-match-score-dialog.component.html',
  styleUrls: ['./set-match-score-dialog.component.scss']
})
export class SetMatchScoreDialogComponent {
    homePoint: number = 0;
    awayPoint: number = 0;
    note: string;

    constructor(public dialog: MatDialogRef<SetMatchScoreDialogComponent>, @Inject(MAT_DIALOG_DATA) public data) { }

    apply() {
        this.dialog.close({
            homePoint: this.homePoint,
            awayPoint: this.awayPoint,
            note: this.note
        });
    }

}
