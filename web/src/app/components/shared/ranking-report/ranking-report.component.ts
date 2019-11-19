import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-ranking-report',
  templateUrl: './ranking-report.component.html',
  styleUrls: ['./ranking-report.component.scss']
})
export class RankingReportComponent implements OnInit {
    @Input() data: any[];

    constructor() { }

    ngOnInit() {
        this.data.forEach((d) => {
            if (!d.ranking) {
                d.difference = '-';
                d.icon = 'ranking-not';
                d.class = 'not';
            } else {
                if (!d.previousRanking) {
                    d.difference = 'yeni';
                    d.icon = 'ranking-new';
                    d.class = 'new';
                } else {
                    d.difference = d.previousRanking - d.ranking;
                    if (d.difference > 0) {
                        d.icon = 'ranking-up';
                        d.class = 'up';
                    } else if (d.difference < 0) {
                        d.icon = 'ranking-down';
                        d.class = 'down';
                    } else if (d.difference === 0) {
                        d.icon = 'ranking-stable';
                        d.class = 'stable';
                    }
                }
            }
        })
    }

}
