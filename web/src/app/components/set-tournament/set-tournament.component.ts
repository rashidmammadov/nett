import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { GameType } from '../../interfaces/game-type';
import { TournamentType } from '../../interfaces/tournament-type';
import { UserType } from '../../interfaces/user-type';
import {FormControl, FormGroup, Validators} from "@angular/forms";
import {first, map} from "rxjs/operators";

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
    date = new Date();
    defaultDate = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate() + 7);

    tournamentForm = new FormGroup({
        gameId: new FormControl(1, [Validators.required]),
        tournamentType: new FormControl('', [Validators.required]),
        days: new FormControl(1, [Validators.required]),
        participantCount: new FormControl(16, [Validators.min(16), Validators.max(32)]),
        startDate: new FormControl(this.defaultDate.getTime(), [Validators.required])
    });

    constructor(private activatedRoute: ActivatedRoute, private user: Store<{user: UserType}>) {
        for (let i = 16; i <= 32; i++) this.list.push(i);
    }

    async ngOnInit() {
        this.games = (await this.activatedRoute.data.pipe(first()).toPromise()).games.data as GameType[];
        this.games.length && (this.tournamentData.game = this.games[0]);
        this.tournamentData.holder = await this.user.select('user').pipe(first()).toPromise();
    }

    public changeTournamentGame() {
        const selectedGameId = this.tournamentForm.controls.gameId.value;
        const game = this.games.find((game: GameType) => game.gameId === selectedGameId);
        this.types = game.playingType;
        this.tournamentData.game = game;
    }

    public changeParticipantCount() {
        this.tournamentData.participantCount = this.tournamentForm.controls.participantCount.value;
    }

}
