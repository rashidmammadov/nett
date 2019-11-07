import { Injectable } from '@angular/core';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { ErrorResponse } from '../../models/error-response';
import { ToastService } from '../toast/toast.service';
import { HttpParams } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class UtilityService {

    public static handleResponseFromService(result: IHttpResponse | ErrorResponse, successCallback: (result) => void): void {
        if ((result as IHttpResponse).status === 'success') {
            successCallback(result);
        } else if ((result as IHttpResponse).status === 'error') {
            ToastService.show((result as IHttpResponse).message)
        } else if (result instanceof ErrorResponse) {
            ToastService.show(result.message);
        }
    }

    public static pipeHttpResponse(response: Observable<IHttpResponse>): Promise<ErrorResponse | IHttpResponse> {
        return response.pipe(catchError((error: { message: string }) => of(new ErrorResponse(error.message)))).toPromise();
    }

    public static setHttpParams(params) {
        let body = new HttpParams();
        Object.keys(params).forEach((key: string) => {
            body = body.set(key, params[key])
        });
        return body;
    }

}
