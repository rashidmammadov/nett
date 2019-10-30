import { Component, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { TournamentService } from '../../services/tournament/tournament.service';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { TournamentType } from '../../interfaces/tournament-type';
import { loaded, loading } from '../../store/actions/progress.action';

@Component({
  selector: 'app-search',
  templateUrl: './search.component.html',
  styleUrls: ['./search.component.scss']
})
export class SearchComponent implements OnInit {
    private _searchResult: TournamentType[] = [];

    constructor(private progress: Store<{progress: boolean}>, private tournamentService: TournamentService) { }

    ngOnInit() {
        this.fetchSearchResult();
    }

    private fetchSearchResult = async () => {
        this.progress.dispatch(loading());
        const result = await this.tournamentService.search(1);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.searchResult = response.data;
        });
        this.progress.dispatch(loaded());
    };

    get searchResult(): TournamentType[] {
        return this._searchResult;
    }

    set searchResult(value: TournamentType[]) {
        this._searchResult = value;
    }

}
