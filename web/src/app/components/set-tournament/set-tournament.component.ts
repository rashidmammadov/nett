import { Component, OnInit } from '@angular/core';
import {ActivatedRoute, Router} from '@angular/router';
import { Store } from '@ngrx/store';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { first } from 'rxjs/operators';
import { GameType } from '../../interfaces/game-type';
import { TournamentType } from '../../interfaces/tournament-type';
import { UserType } from '../../interfaces/user-type';
import { TournamentService } from '../../services/tournament/tournament.service';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { loaded, loading } from '../../store/actions/progress.action';

@Component({
  selector: 'app-set-tournament',
  templateUrl: './set-tournament.component.html',
  styleUrls: ['./set-tournament.component.scss']
})
export class SetTournamentComponent implements OnInit {
    public list: number[] = [];
    public games: GameType[] = [];
    public types: string[] = [];
    public tournamentData: TournamentType = <TournamentType>Object();
    public income: number = 0.00;
    date = new Date();
    minDate = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate() + 7, 12, 0, 0);
    maxDate = new Date(this.date.getFullYear(), this.date.getMonth() + 4, this.date.getDate(), 12, 0, 0);

    tournamentForm = new FormGroup({
        gameId: new FormControl(1, [Validators.required]),
        tournamentType: new FormControl('knock_out', [Validators.required]),
        days: new FormControl(1, [Validators.required]),
        participantCount: new FormControl(16, [Validators.min(16), Validators.max(32)]),
        startDate: new FormControl(this.minDate, [Validators.required])
    });

    constructor(private activatedRoute: ActivatedRoute, private user: Store<{user: UserType}>, private router: Router,
                private tournamentService: TournamentService, private progress: Store<{progress: boolean}>) {
        for (let i = 16; i <= 32; i++) this.list.push(i);
    }

    async ngOnInit() {
        this.games = (await this.activatedRoute.data.pipe(first()).toPromise()).games.data as GameType[];
        this.games.length && (this.tournamentData.game = this.games[0]);
        this.tournamentData.holder = await this.user.select('user').pipe(first()).toPromise();
        this.changeTournamentGame();
        this.changeParticipantCount();
    }

    public changeTournamentGame() {
        const selectedGameId = this.tournamentForm.controls.gameId.value;
        const game = this.games.find((game: GameType) => Number(game.gameId) === Number(selectedGameId));
        this.types = game.playingType;
        this.tournamentForm.controls.tournamentType.setValue(this.types[0]);
        this.tournamentData.game = game;
    }

    public changeParticipantCount() {
        const participantCount = this.tournamentForm.controls.participantCount.value;
        this.tournamentData.participantCount = participantCount;
        this.income = Number((Number(participantCount) * (5 + Number(participantCount) / 11)).toFixed(2));
    }

    public submit = async () => {
        if (this.tournamentForm.valid) {
            this.progress.dispatch(loading());
            const result = await this.tournamentService.add(this.setTournamentFormData());
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                this.router.navigateByUrl('app/home');
            });
            this.progress.dispatch(loaded());
        }
    };

    private setTournamentFormData() {
        let form = this.tournamentForm.controls;
        return {
            'gameId': form.gameId.value,
            'tournamentType': form.tournamentType.value,
            'days': form.days.value,
            'participantCount': form.participantCount.value,
            'startDate': new Date(form.startDate.value).getTime()
        }
    }
}
