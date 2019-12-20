import { Component, OnInit } from '@angular/core';
import { MatDialogRef } from '@angular/material/dialog';
import { FinanceService } from '../../../services/finance/finance.service';
import { UtilityService } from '../../../services/utility/utility.service';
import { IHttpResponse } from '../../../interfaces/i-http-response';
import { TYPES } from '../../../constants/types.constant';
import { FinanceArchiveType } from '../../../interfaces/finance-archive-type';

@Component({
  selector: 'app-finance-archive-dialog',
  templateUrl: './finance-archive-dialog.component.html',
  styleUrls: ['./finance-archive-dialog.component.scss']
})
export class FinanceArchiveDialogComponent implements OnInit {

    data: FinanceArchiveType[] = [];
    currentPage: number = 1;
    itemPerPage: number = 10;
    totalPage: number = 1;
    progress: boolean = false;
    public FINANCE_STATUS = TYPES.FINANCE_STATUS;
    public FINANCE_TYPE = TYPES.FINANCE_TYPE;

    constructor(public dialog: MatDialogRef<FinanceArchiveDialogComponent>,
                private financeService: FinanceService) { }

    async ngOnInit() {
        this.fetchData();
    }

    close(): void {
        this.dialog.close();
    }

    async loadMore() {
        this.currentPage += 1;
        this.fetchData();
    }

    private fetchData = async () => {
        this.progress = true;
        const result = await this.financeService.get(this.currentPage, this.itemPerPage);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            response.data.forEach((data) => {data.date = UtilityService.dateFromNow(data.date)});
            this.totalPage = response.total_page;
            this.data = this.data.concat(response.data);
        });
        this.progress = false;
    };

}
