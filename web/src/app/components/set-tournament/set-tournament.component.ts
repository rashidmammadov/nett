import { Component, OnInit } from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { GameType } from '../../interfaces/game-type';

@Component({
  selector: 'app-set-tournament',
  templateUrl: './set-tournament.component.html',
  styleUrls: ['./set-tournament.component.scss']
})
export class SetTournamentComponent implements OnInit {
    public games: GameType[] = [];

    constructor(private activatedRoute: ActivatedRoute) { }

    ngOnInit() {
        this.activatedRoute.data.subscribe((result) => {
            this.games = result.games.data;
        });
    }

}
