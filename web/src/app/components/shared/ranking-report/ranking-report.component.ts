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
    }

}
