import { Component, Input, OnInit } from '@angular/core';
import { RankingReportType } from '../../../interfaces/ranking-report-type';

@Component({
  selector: 'app-ranking-report',
  templateUrl: './ranking-report.component.html',
  styleUrls: ['./ranking-report.component.scss']
})
export class RankingReportComponent implements OnInit {
    @Input() data: RankingReportType[];

    constructor() { }

    ngOnInit() {
        this.data.forEach((d) => {
            if (!d.ranking) {
                this.prepareRankingClassAndIcon(d, '-', 'ranking-not', 'not');
            } else if (!d.previousRanking) {
                this.prepareRankingClassAndIcon(d, 'yeni', 'ranking-new', 'new');
            } else {
                d.difference = d.previousRanking - d.ranking;
                if (d.difference > 0) {
                    this.prepareRankingClassAndIcon(d, d.difference, 'ranking-up', 'up');
                } else if (d.difference < 0) {
                    this.prepareRankingClassAndIcon(d, d.difference, 'ranking-down', 'down');
                } else if (d.difference === 0) {
                    this.prepareRankingClassAndIcon(d, d.difference, 'ranking-stable', 'stable');
                }
            }

        });
    }

    private prepareRankingClassAndIcon(data: RankingReportType, difference: string | number, iconName: string, className: string) {
        data.difference = difference;
        data.icon = iconName;
        data.class = className;
    }

}
