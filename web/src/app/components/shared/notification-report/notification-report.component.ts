import { Component, Input, OnInit } from '@angular/core';
import { TYPES } from '../../../constants/types.constant';
import { NotificationReportType } from '../../../interfaces/notification-report-type';

@Component({
  selector: 'app-notification-report',
  templateUrl: './notification-report.component.html',
  styleUrls: ['./notification-report.component.scss']
})
export class NotificationReportComponent implements OnInit {
    @Input() data: NotificationReportType[];

    constructor() { }

    ngOnInit() {
        this.data.forEach((notification: NotificationReportType) => {
            if (notification.status === TYPES.TOURNAMENT_STATUS.ACTIVE) {
                 notification.class = 'active';
                 notification.icon = 'tournament-active';
            } else if (notification.status === TYPES.TOURNAMENT_STATUS.CANCEL) {
              notification.class = 'cancel';
              notification.icon = 'tournament-cancel';
            } else if (notification.status === TYPES.TOURNAMENT_STATUS.CLOSE) {
                notification.class = 'close';
                notification.icon = 'tournament-close';
            } else if (notification.status === TYPES.TOURNAMENT_STATUS.OPEN) {
                notification.class = 'open';
                notification.icon = 'tournament-open';
            }
        });
    }

}
