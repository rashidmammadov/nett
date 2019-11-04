import { Component, Input, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { AttendDialogComponent } from '../attend-dialog/attend-dialog.component';
import { TournamentType } from '../../../interfaces/tournament-type';
import { UserType } from '../../../interfaces/user-type';

@Component({
  selector: 'app-attend-button',
  templateUrl: './attend-button.component.html',
  styleUrls: ['./attend-button.component.scss']
})
export class AttendButtonComponent implements OnInit {
    @Input() tournament: TournamentType;
    user: UserType;

    constructor(private dialog: MatDialog, private store: Store<{user: UserType}>) {
        this.getUserData();
    }

    ngOnInit() {
    }

    attendDialog() {
        const attendDialog = this.dialog.open(AttendDialogComponent, {
            data: this.user
        });

        attendDialog.afterClosed().toPromise().then(result => {
            console.log(result)
        });
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    }

}
