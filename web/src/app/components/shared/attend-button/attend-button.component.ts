import { Component, Input, OnInit } from '@angular/core';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { AttendDialogComponent } from '../attend-dialog/attend-dialog.component';
import { TournamentType } from '../../../interfaces/tournament-type';
import { UserType } from '../../../interfaces/user-type';
import { ParticipantService } from '../../../services/participant/participant.service';
import { loaded, loading } from '../../../store/actions/progress.action';
import { UtilityService } from '../../../services/utility/utility.service';
import { IHttpResponse } from '../../../interfaces/i-http-response';
import { TYPES } from '../../../constants/types.constant';
import {ToastService} from "../../../services/toast/toast.service";
import {set} from "../../../store/actions/user.action";
import {LeaveDialogComponent} from "../leave-dialog/leave-dialog.component";

@Component({
  selector: 'app-attend-button',
  templateUrl: './attend-button.component.html',
  styleUrls: ['./attend-button.component.scss']
})
export class AttendButtonComponent implements OnInit {
    @Input() tournament: TournamentType;
    user: UserType;
    tournamentStatus = TYPES.TOURNAMENT_STATUS;
    loading: boolean = false;

    constructor(private participantService: ParticipantService, private progress: Store<{progress: boolean}>,
                private dialog: MatDialog, private store: Store<{user: UserType}>) {
        this.getUserData();
    }

    ngOnInit() {
    }

    attendLeaveDialog(attended) {
        if (attended) {
            this.dialog.open(LeaveDialogComponent)
            .afterClosed().toPromise().then(result => {
                if (result) {
                    this.leaveTournament(Number(this.tournament.tournamentId));
                }
            });
        } else {
            this.dialog.open(AttendDialogComponent, {data: this.user})
            .afterClosed().toPromise().then(result => {
                if (result) {
                    const params = {tournamentId: Number(this.tournament.tournamentId), paymentType: result};
                    this.attendTournament(params);
                }
            });
        }
    }

    private attendTournament = async (params) => {
        this.progress.dispatch(loading());
        this.loading = true;
        const result = await this.participantService.attend(params);
        this.setUpdatedData(result, true);
        this.progress.dispatch(loaded());
        this.loading = false;
    };

    private leaveTournament = async (tournamentId: number) => {
        this.progress.dispatch(loading());
        this.loading = true;
        const result = await this.participantService.leave(tournamentId);
        this.setUpdatedData(result, false);
        this.progress.dispatch(loaded());
        this.loading = false;
    };

    private setUpdatedData(result, attended) {
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.tournament.attended = attended;
            this.tournament.currentParticipants = response.data.currentParticipants;
            this.user.budget = response.data.budget;
            this.user.ticket = response.data.ticket;
            this.store.dispatch(set({user: this.user}));
            ToastService.show(response.message);
        });
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    };

}
