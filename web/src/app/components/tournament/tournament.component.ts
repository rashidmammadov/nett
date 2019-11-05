import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { first } from 'rxjs/operators';
import { Store } from '@ngrx/store';
import { loaded } from '../../store/actions/progress.action';
import { TournamentType } from '../../interfaces/tournament-type';

@Component({
  selector: 'app-tournament',
  templateUrl: './tournament.component.html',
  styleUrls: ['./tournament.component.scss']
})
export class TournamentComponent implements OnInit {
    private _tournament: TournamentType;

    constructor(private activatedRoute: ActivatedRoute, private progress: Store<{progress: boolean}>) {
        this.getTournamentData();
    }

    ngOnInit() {
    }

    private getTournamentData = async () => {
        const result = await this.activatedRoute.data.pipe(first()).toPromise();
        if (result.tournament && result.tournament.data) {
            this.tournament = result.tournament.data as TournamentType;
        }
        this.progress.dispatch(loaded());
    };

    get tournament(): TournamentType {
        return this._tournament;
    }

    set tournament(value: TournamentType) {
        this._tournament = value;
    }

}
