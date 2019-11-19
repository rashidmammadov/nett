import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-notification-report',
  templateUrl: './notification-report.component.html',
  styleUrls: ['./notification-report.component.scss']
})
export class NotificationReportComponent implements OnInit {
    @Input() data: any[];

    constructor() { }

    ngOnInit() {
    }

}
