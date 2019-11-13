import { Component, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { TimelineReportType } from '../../interfaces/timeline-report-type';
import { ReportService } from '../../services/report/report.service';
import { TYPES } from '../../constants/types.constant';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { loaded, loading } from '../../store/actions/progress.action';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
    timelineReportData: TimelineReportType[] = [];

    constructor(private reportService: ReportService, private store: Store<{progress: boolean}>) { }

    ngOnInit() {
        this.fetchTimelineReport();
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

}
