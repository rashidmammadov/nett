import { Injectable } from '@angular/core';
import {HttpClient, HttpParams} from "@angular/common/http";
import {ErrorResponse} from "../../models/error-response";
import {IHttpResponse} from "../../interfaces/i-http-response";
import {UtilityService} from "../utility/utility.service";
import {ENDPOINTS} from "../../constants/endpoints.constant";
import {stringify} from "querystring";

@Injectable({
  providedIn: 'root'
})
export class ParticipantService {

    constructor(private http: HttpClient) { }

    attend(params): Promise<ErrorResponse | IHttpResponse> {
        let body = new HttpParams();
        Object.keys(params).forEach((key: string) => {
          body = body.set(key, params[key])
        });
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.PARTICIPANTS(), body));
    }

    leave(tournamentId: number): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.delete<IHttpResponse>(ENDPOINTS.PARTICIPANTS(tournamentId)));
    }

}
