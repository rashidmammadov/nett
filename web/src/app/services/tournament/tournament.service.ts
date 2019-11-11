import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ErrorResponse } from '../../models/error-response';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { ENDPOINTS } from '../../constants/endpoints.constant';
import { UtilityService } from '../utility/utility.service';

@Injectable({
  providedIn: 'root'
})
export class TournamentService {

    constructor(private http: HttpClient) { }

    add(params): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.TOURNAMENTS(), UtilityService.setHttpParams(params)));
    }

    search(status: number | string = 0): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.TOURNAMENTS(null, status)));
    }

    myTournaments(status: number | string = 0): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.MY_TOURNAMENTS(status)));
    }

    get(tournamentId: number | string): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.TOURNAMENTS(tournamentId)));
    }
}
