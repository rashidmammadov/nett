import { Injectable, Injector } from '@angular/core';
import { HttpParams } from '@angular/common/http';
import { Router } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { ErrorResponse } from '../../models/error-response';
import { ToastService } from '../toast/toast.service';
import { Cookie } from '../cookie/cookies.service';
import { MESSAGES } from '../../constants/messages.constant';
import { environment } from '../../../environments/environment';
import { DATE_TIME } from '../../constants/date-time.constant';

@Injectable({
  providedIn: 'root'
})
export class UtilityService {

    public static injector: Injector;

    public static handleResponseFromService(result: IHttpResponse | ErrorResponse, successCallback: (result) => void): void {
        if (!navigator.onLine) {
            ToastService.show(MESSAGES.ERROR.ERR_INTERNET_DISCONNECTED);
        } else if ((result as IHttpResponse).status === 'success') {
            successCallback(result);
        } else if ((result as IHttpResponse).status === 'error') {
            ToastService.show((result as IHttpResponse).message)
        } else if (result instanceof ErrorResponse) {
            ToastService.show(result.message);
            if (result.status_code === 401) {
                Cookie.delete('_nrt');
                UtilityService.injector.get(Router).navigateByUrl('login');
            }
        }
    }

    public static pipeHttpResponse(response: Observable<IHttpResponse>): Promise<ErrorResponse | IHttpResponse> {
        return response.pipe(catchError((err) => of(new ErrorResponse(err.error)))).toPromise();
    }

    public static prepareGoogleMap(address: string) {
        let result = '';
        if (address) {
            result = `<iframe frameborder="0" class="google-map-frame" 
                src="${environment.googleMapApi}q=${address}&key=${environment.googleMapId}" allowfullscreen></iframe>`;
        }
        return this.injector.get(DomSanitizer).bypassSecurityTrustHtml(result);
    }

    public static setHttpParams(params) {
        let body = new HttpParams();
        Object.keys(params).forEach((key: string) => {
            body = body.set(key, params[key])
        });
        return body;
    }

    public static millisecondsToDate(milliseconds, format = null): Date {
        if (typeof milliseconds === 'string' || typeof milliseconds === 'number') {
            let date: any = new Date(Number(milliseconds));
            const day = date.getDate();
            const monthIndex = date.getMonth();
            const year = date.getFullYear();
            const hour = date.getHours() < 10 ? '0' + date.getHours() : date.getHours();
            const minute = date.getMinutes() < 10 ? '0' + date.getMinutes() : date.getMinutes();
            if (format === DATE_TIME.FORMAT.DATE) {
                date = `${day} ${DATE_TIME.MONTHS_MAP[monthIndex]} ${year}`;
            } else if (format === DATE_TIME.FORMAT.DATE_TIME) {
                date = `${day} ${DATE_TIME.MONTHS_MAP[monthIndex]} ${year} ${hour}:${minute}`;
            }
            return date;
        } else {
            return milliseconds;
        }
    }

}
