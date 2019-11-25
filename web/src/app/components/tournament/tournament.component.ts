import { Component } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { first } from 'rxjs/operators';
import { MatDialog } from '@angular/material/dialog';
import { Store } from '@ngrx/store';
import { loaded, loading } from '../../store/actions/progress.action';
import { TournamentType } from '../../interfaces/tournament-type';
import { TYPES } from '../../constants/types.constant';
import { SetMatchScoreDialogComponent } from '../shared/set-match-score-dialog/set-match-score-dialog.component';
import { ParticipantType } from '../../interfaces/participant-type';
import { FixtureService } from '../../services/fixture/fixture.service';
import { UtilityService } from '../../services/utility/utility.service';
import { ToastService } from '../../services/toast/toast.service';

@Component({
  selector: 'app-tournament',
  templateUrl: './tournament.component.html',
  styleUrls: ['./tournament.component.scss']
})
export class TournamentComponent {
    public googleMap: string;
    private _tournament: TournamentType;

    tournamentType = TYPES.TOURNAMENT_TYPE;

    constructor(private activatedRoute: ActivatedRoute, private progress: Store<{progress: boolean}>,
                private dialog: MatDialog, private fixtureService: FixtureService) {
        this.getTournamentData().then();
    }

    openSetMatchScoreDialog(tourId: number, matchId: number, home: ParticipantType, away: ParticipantType) {
        this.dialog.open(SetMatchScoreDialogComponent, {
            data: {
                home: home,
                away: away
            }
        })
        .afterClosed().toPromise().then(result => {
            !!result && this.sendSetMatchScore(this.setMatchScoreData(tourId, matchId, result.homePoint, result.awayPoint));
        });
    }

    private getTournamentData = async () => {
        const result = await this.activatedRoute.data.pipe(first()).toPromise();
        if (result.tournament && result.tournament.data) {
            this.tournament = result.tournament.data as TournamentType;
            this.googleMap = UtilityService.prepareGoogleMap('offside%20playstation%20k%C3%BC%C3%A7%C3%BCkpark');
        }
        this.progress.dispatch(loaded());
    };

    private sendSetMatchScore = async (params) => {
        this.progress.dispatch(loading());
        const result = await this.fixtureService.setKnockOutMatchScore(params);
        UtilityService.handleResponseFromService(result, (response) => {
            this.tournament.fixture = response.data;
            ToastService.show(response.message);
        });
        this.progress.dispatch(loaded());
    };

    private setMatchScoreData(tourId: number, matchId: number, homePoint: number, awayPoint: number) {
        return {
            'tournamentId': this.tournament.tournamentId,
            'tournamentType': this.tournament.tournamentType,
            'tourId': tourId,
            'matchId': matchId,
            'homePoint': homePoint,
            'awayPoint': awayPoint
        }
    }

    get tournament(): TournamentType {
        return this._tournament;
    }

    set tournament(value: TournamentType) {
        this._tournament = value;
    }

}
