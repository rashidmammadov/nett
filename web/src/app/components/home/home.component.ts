import { Component, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { ReportService } from '../../services/report/report.service';
import { TYPES } from '../../constants/types.constant';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { loaded, loading } from '../../store/actions/progress.action';
import { TimelineReportType } from '../../interfaces/timeline-report-type';
import { PieChartReportType } from '../../interfaces/pie-chart-report-type';
import { NotificationReportType } from '../../interfaces/notification-report-type';
import { RankingReportType } from '../../interfaces/ranking-report-type';
import { UserType } from '../../interfaces/user-type';
import { MostPlayedReportType } from '../../interfaces/most-played-report-type';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.scss']
})
export class HomeComponent implements OnInit {
    user: UserType;
    financeReportData: PieChartReportType[] = [];
    mostPlayedReportData: MostPlayedReportType[] = [];
    notificationReportData: NotificationReportType[] = [];
    rankingReportData: RankingReportType[] = [];
    timelineReportData: TimelineReportType[] = [];

    constructor(private reportService: ReportService, private store: Store<{progress: boolean, user: UserType}>) {
        this.getUserData().then();
    }

    ngOnInit() {
        if (this.user.type === TYPES.USER.HOLDER) {
            this.fetchReport(TYPES.REPORT.FINANCE, 'financeReportData').then();
            this.fetchReport(TYPES.REPORT.MOST_PLAYED, 'mostPlayedReportData').then();
            this.fetchReport(TYPES.REPORT.NOTIFICATION, 'notificationReportData').then();
        } else if (this.user.type === TYPES.USER.PLAYER) {
            this.fetchReport(TYPES.REPORT.FINANCE, 'financeReportData').then();
            this.fetchReport(TYPES.REPORT.NOTIFICATION, 'notificationReportData').then();
            this.fetchReport(TYPES.REPORT.TIMELINE, 'timelineReportData').then();
            this.fetchReport(TYPES.REPORT.RANKING, 'rankingReportData').then();
        }
    }

    private fetchReport = async (reportType: string, property: string) => {
        this.store.dispatch(loading());
        const result = await this.reportService.get(reportType);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this[property] = response.data;
        });
        this.store.dispatch(loaded());
    };

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
    };

}
