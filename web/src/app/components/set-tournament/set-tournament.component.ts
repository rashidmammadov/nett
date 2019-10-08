import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { Store } from '@ngrx/store';
import { GameType } from '../../interfaces/game-type';
import { TournamentType } from '../../interfaces/tournament-type';
import { UserType } from '../../interfaces/user-type';

@Component({
  selector: 'app-set-tournament',
  templateUrl: './set-tournament.component.html',
  styleUrls: ['./set-tournament.component.scss']
})
export class SetTournamentComponent implements OnInit {
    public games: GameType[] = [];
    public tournamentData: TournamentType = <TournamentType>Object();

    constructor(private activatedRoute: ActivatedRoute, private user: Store<{user: UserType}>) { }

    ngOnInit() {
        this.activatedRoute.data.subscribe((result) => {
            this.games = result.games.data;
            this.tournamentData.game = this.games[0];
        });
        this.user.select('user').subscribe((data: UserType) => {
            this.tournamentData.holder = data;
        });
    }

}
