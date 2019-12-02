import { Component, OnInit } from '@angular/core';
import { MatTabChangeEvent } from '@angular/material';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { TournamentService } from '../../services/tournament/tournament.service';
import { TYPES } from '../../constants/types.constant';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { TournamentType } from '../../interfaces/tournament-type';
import { loaded, loading } from '../../store/actions/progress.action';
import { UserType } from '../../interfaces/user-type';

@Component({
  selector: 'app-belong',
  templateUrl: './belong.component.html',
  styleUrls: ['./belong.component.scss']
})
export class BelongComponent implements OnInit {
    user: UserType;
    tabs: {label: string, loaded: boolean, data: TournamentType[], status: number}[];
    activeTabNumber: number;

    constructor(private tournamentService: TournamentService, private store: Store<{user: UserType, progress: boolean}>) {
        this.getUserData().then();
    }

    ngOnInit() {
        this.selectedTabChange();
    }

    fetchTournaments = async (tab: number, status: number = 0) => {
        this.store.dispatch(loading());
        const result = await this.tournamentService.myTournaments(status);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.tabs[tab].data = response.data;
            this.tabs[tab].loaded = true;
        });
        this.store.dispatch(loaded());
    };

    selectedTabChange(event?: MatTabChangeEvent) {
        this.activeTabNumber = event ? event.index : 0;
        if (!this.tabs[this.activeTabNumber].loaded) {
            this.fetchTournaments(this.activeTabNumber, this.tabs[this.activeTabNumber].status).then();
        }
    }

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
        this.prepareDefaultTab(this.user.type);
    };

    private prepareDefaultTab(userType) {
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
        }];
        if (userType === TYPES.USER.HOLDER) {
            this.tabs.push({
                label: 'İptal Edilenler',
                loaded: false,
                data: [],
                status: TYPES.TOURNAMENT_STATUS.CANCEL
            });
        }
    }

}
