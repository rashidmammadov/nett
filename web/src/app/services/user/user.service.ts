import { Injectable } from '@angular/core';
import { HttpClient, HttpParams } from '@angular/common/http';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { ErrorResponse } from '../../models/error-response';
import { UtilityService } from '../utility/utility.service';
import { ENDPOINTS } from '../../constants/endpoints.constant';

@Injectable({
  providedIn: 'root'
})
export class UserService {

    constructor(private http: HttpClient) { }

    activate(params): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.put<IHttpResponse>(ENDPOINTS.ACTIVATE(),
            UtilityService.setHttpParams(params)))
    }

    login(email: string, password: string | number): Promise<ErrorResponse | IHttpResponse> {
        let body = new HttpParams();
        body = body.set('email', email);
        body = body.set('password', password.toString());
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.LOGIN(), body));
    }

    logout(): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.LOGOUT(), {}));
    }

    refreshUser(): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.get<IHttpResponse>(ENDPOINTS.REFRESH_USER()));
    }

    register(params): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.REGISTER(),
            UtilityService.setHttpParams(params)));
    }

    resetPassword(params): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.post<IHttpResponse>(ENDPOINTS.RESET_PASSWORD(),
            UtilityService.setHttpParams(params)));
    }

    updatePassword(params): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.put<IHttpResponse>(ENDPOINTS.UPDATE_PASSWORD(),
            UtilityService.setHttpParams(params)));
    }

    updateSettings(params): Promise<ErrorResponse | IHttpResponse> {
        return UtilityService.pipeHttpResponse(this.http.put<IHttpResponse>(ENDPOINTS.UPDATE_SETTINGS(),
            UtilityService.setHttpParams(params)));
    }

}
