import { Component, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { ReportService } from '../../services/report/report.service';
import { TYPES } from '../../constants/types.constant';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { loaded, loading } from '../../store/actions/progress.action';
import { TimelineReportType } from '../../interfaces/timeline-report-type';
import { PieChartReportType } from '../../interfaces/pie-chart-report-type';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
    financeReportData: PieChartReportType[] = [];
    timelineReportData: TimelineReportType[] = [];
    rankingReportData: [] = [];

    constructor(private reportService: ReportService, private store: Store<{progress: boolean}>) { }

    ngOnInit() {
        this.fetchFinanceReport();
        this.fetchTimelineReport();
        this.fetchRankingReport();
    }

    private fetchTimelineReport = async () => {
        this.store.dispatch(loading());
        const result = await this.reportService.get(TYPES.REPORT.TIMELINE);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.timelineReportData = response.data;
            this.timelineReportData.forEach((timelineData: TimelineReportType) => {
                timelineData.startDate = UtilityService.millisecondsToDate(timelineData.startDate);
            });
        });
        this.store.dispatch(loaded());
    };

    private fetchRankingReport = async () => {
        this.store.dispatch(loading());
        const result = await this.reportService.get(TYPES.REPORT.RANKING);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.rankingReportData = response.data;
        });
        this.store.dispatch(loaded());
    };

    private fetchFinanceReport = async () => {
        this.store.dispatch(loading());
        const result = await this.reportService.get(TYPES.REPORT.FINANCE);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.financeReportData = response.data;
        });
        this.store.dispatch(loaded());
    };

}
