import { Injectable } from '@angular/core';
import {HttpClient, HttpParams} from '@angular/common/http';
import { ErrorResponse } from "../../models/error-response";
import { IHttpResponse } from '../../interfaces/i-http-response';
import { UtilityService } from '../utility/utility.service';
import { ENDPOINTS } from '../../constants/endpoints.constant';

@Injectable({
  providedIn: 'root'
})
export class GameService {

    constructor(private http: HttpClient) { }

    get(id:number = null): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.GAMES(id)));
    }

    set(params): Promise<ErrorResponse | IHttpResponse> {
        let body = new HttpParams();
        Object.keys(params).forEach((key: string) => {
            body = body.set(key, params[key])
        });
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.GAMES(), body));
    }
}
