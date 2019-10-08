import { Component, OnInit, Input, SimpleChanges, OnChanges } from '@angular/core';
import { TournamentType } from '../../../interfaces/tournament-type';

@Component({
  selector: 'app-tournament-card',
  templateUrl: './tournament-card.component.html',
  styleUrls: ['./tournament-card.component.scss']
})
export class TournamentCardComponent implements OnInit, OnChanges {
    @Input() data: TournamentType;

    constructor() { }

    ngOnInit() {
    }

    ngOnChanges(changes: SimpleChanges): void {
        console.log(changes.data.currentValue);
    }
}
