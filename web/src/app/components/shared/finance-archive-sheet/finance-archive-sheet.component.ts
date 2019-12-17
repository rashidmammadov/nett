import { Component, OnInit } from '@angular/core';
import { MatBottomSheetRef } from '@angular/material/bottom-sheet';
import { FinanceService } from '../../../services/finance/finance.service';
import { UtilityService } from '../../../services/utility/utility.service';
import { IHttpResponse } from '../../../interfaces/i-http-response';

@Component({
  selector: 'app-finance-archive-sheet',
  templateUrl: './finance-archive-sheet.component.html',
  styleUrls: ['./finance-archive-sheet.component.scss']
})
export class FinanceArchiveSheetComponent implements OnInit {

    financeArchiveData = [];

    activePage: number = 1;

    itemPerPage: number = 1;

    loading: boolean = false;

    constructor(private bottomSheetRef: MatBottomSheetRef<FinanceArchiveSheetComponent>, private financeService: FinanceService) { }

    async ngOnInit() {
        this.fetchData();
    }

    closeSheet(event: MouseEvent): void {
        this.bottomSheetRef.dismiss();
        event.preventDefault();
    }

    loadMore() {
        this.activePage += 1;
        this.fetchData();
    }

    private fetchData = async () => {
        this.loading = true;
        const result = await this.financeService.get(this.activePage, this.itemPerPage);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.financeArchiveData = this.financeArchiveData.concat(response.data);
        });
        this.loading = false;
    };

}
