import { Component, Inject, OnInit } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef } from '@angular/material/dialog';

@Component({
  selector: 'app-set-match-score-dialog',
  templateUrl: './set-match-score-dialog.component.html',
  styleUrls: ['./set-match-score-dialog.component.scss']
})
export class SetMatchScoreDialogComponent implements OnInit {
    homePoint: number = 0;
    awayPoint: number = 0;

    constructor(public dialog: MatDialogRef<SetMatchScoreDialogComponent>, @Inject(MAT_DIALOG_DATA) public data) { }

    ngOnInit() {
    }

    apply() {
        this.dialog.close({
            homePoint: this.homePoint,
            awayPoint: this.awayPoint
        });
    }

}
