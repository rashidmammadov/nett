import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
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
        let body = new HttpParams();
        Object.keys(params).forEach((key: string) => {
            body = body.set(key, params[key])
        });
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.TOURNAMENTS(), body));
    }

    search(status: number | string = 0): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.TOURNAMENTS(null, status)));
    }

    myTournaments(status?: number | string): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.MY_TOURNAMENTS(status)));
    }

    get(tournamentId: number | string): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.TOURNAMENTS(tournamentId)));
    }
}
