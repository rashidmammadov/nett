import { Component, Input, OnInit } from '@angular/core';
import { MostPlayedReportType } from '../../../interfaces/most-played-report-type';

@Component({
  selector: 'app-most-played-report',
  templateUrl: './most-played-report.component.html',
  styleUrls: ['./most-played-report.component.scss']
})
export class MostPlayedReportComponent implements OnInit {
    @Input() data: MostPlayedReportType[];

    constructor() { }

    ngOnInit() {
        this.data.sort((a: MostPlayedReportType, b: MostPlayedReportType) => {
            return a.totalCount > b.totalCount ? -1 :  a.totalCount < b.totalCount ? 1 : 0;
        });
    }

}
