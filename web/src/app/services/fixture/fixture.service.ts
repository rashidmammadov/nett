import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { ErrorResponse } from '../../models/error-response';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { UtilityService } from '../utility/utility.service';
import { ENDPOINTS } from '../../constants/endpoints.constant';

@Injectable({
  providedIn: 'root'
})
export class FixtureService {

    constructor(private http: HttpClient) { }

    setKnockOutMatchScore(params): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.put<IHttpResponse>(ENDPOINTS.FIXTURE(), UtilityService.setHttpParams(params)));
    }

}
