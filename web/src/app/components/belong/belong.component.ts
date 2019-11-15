import { Component, OnInit } from '@angular/core';
import { TournamentService } from '../../services/tournament/tournament.service';
import { MatTabChangeEvent } from '@angular/material';
import { Store } from '@ngrx/store';
import { TYPES } from '../../constants/types.constant';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { TournamentType } from '../../interfaces/tournament-type';
import { loaded, loading } from '../../store/actions/progress.action';

@Component({
  selector: 'app-belong',
  templateUrl: './belong.component.html',
  styleUrls: ['./belong.component.scss']
})
export class BelongComponent implements OnInit {
    tabs: {label: string, loaded: boolean, data: TournamentType[], status: number}[];
    activeTabNumber: number;

    constructor(private tournamentService: TournamentService, private progress: Store<{progress: boolean}>) {
        this.prepareDefaultTab();
    }

    ngOnInit() {
        this.selectedTabChange();
    }

    fetchTournaments = async (tab: number, status: number = 0) => {
        this.progress.dispatch(loading());
        const result = await this.tournamentService.myTournaments(status);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.tabs[tab].data = response.data;
            this.tabs[tab].loaded = true;
        });
        this.progress.dispatch(loaded());
    };

    selectedTabChange(event?: MatTabChangeEvent) {
        this.activeTabNumber = event ? event.index : 0;
        if (!this.tabs[this.activeTabNumber].loaded) {
            this.fetchTournaments(this.activeTabNumber, this.tabs[this.activeTabNumber].status);
        }
    }

    private prepareDefaultTab() {
        this.tabs = [{
            label: 'Aktif Turnuvalar',
            loaded: false,
            data: [],
            status: TYPES.TOURNAMENT_STATUS.ACTIVE
        }, {
            label: 'Yakında Başlayacak',
            loaded: false,
            data: [],
            status: TYPES.TOURNAMENT_STATUS.OPEN
        }, {
            label: 'Sonuçlanan Turnuvalar',
            loaded: false,
            data: [],
            status: TYPES.TOURNAMENT_STATUS.CLOSE
        }, {
            label: 'İptal Edilenler',
            loaded: false,
            data: [],
            status: TYPES.TOURNAMENT_STATUS.CANCEL
        }]
    }

}
