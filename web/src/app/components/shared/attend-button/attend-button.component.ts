import { Component, Input, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { AttendDialogComponent } from '../attend-dialog/attend-dialog.component';
import { TournamentType } from '../../../interfaces/tournament-type';
import { UserType } from '../../../interfaces/user-type';
import {ParticipantService} from "../../../services/participant/participant.service";
import {loaded, loading} from "../../../store/actions/progress.action";
import {UtilityService} from "../../../services/utility/utility.service";
import {IHttpResponse} from "../../../interfaces/i-http-response";

@Component({
  selector: 'app-attend-button',
  templateUrl: './attend-button.component.html',
  styleUrls: ['./attend-button.component.scss']
})
export class AttendButtonComponent implements OnInit {
    @Input() tournament: TournamentType;
    user: UserType;

    constructor(private participantService: ParticipantService, private progress: Store<{progress: boolean}>,
                private dialog: MatDialog, private store: Store<{user: UserType}>) {
        this.getUserData();
    }

    ngOnInit() {
    }

    attendDialog() {
        this.dialog.open(AttendDialogComponent, {
            data: this.user
        }).afterClosed().toPromise().then(result => {
            const params = {tournamentId: Number(this.tournament.tournamentId), paymentType: result};
            this.attendTournament(params);
        });
    }

    private attendTournament = async (params) => {
        this.progress.dispatch(loading());
        const result = await this.participantService.attend(params);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            console.log(result);
        });
        this.progress.dispatch(loaded());
    };

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    }

}
