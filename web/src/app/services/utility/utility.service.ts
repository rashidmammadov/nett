import { Injectable, Injector } from '@angular/core';
import { HttpParams } from '@angular/common/http';
import { Router } from '@angular/router';
import { DomSanitizer } from '@angular/platform-browser';
import { FormGroup, Validators } from '@angular/forms';
import { Observable, of } from 'rxjs';
import { catchError } from 'rxjs/operators';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { ErrorResponse } from '../../models/error-response';
import { ToastService } from '../toast/toast.service';
import { Cookie } from '../cookie/cookies.service';
import { MESSAGES } from '../../constants/messages.constant';
import { environment } from '../../../environments/environment';
import { DATE_TIME } from '../../constants/date-time.constant';
import { TYPES } from 'src/app/constants/types.constant';
import { REGEX } from '../../constants/regex.constant';

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

    public static calculateAppCommission(participantCount, participationFee) {
        return Number((((participantCount * participationFee) * 12.5) / 100).toFixed(2));
    }

    public static calculateHolderEarnings(participantCount, participationFee) {
        return Number((participantCount * ((participationFee * TYPES.MIN_COEFFICIENT) / TYPES.MIN_PARTICIPATION_FEE)).toFixed(2));
    }

    public static calculateDepositCommission(amount: number, percent: number, additionalFee?: number): number {
        const commission = ((amount + (additionalFee || 0)) / (1 - (percent / 100))).toFixed(2);
        return Number(commission);
    }

    public static calculateWinnersEarnings(participantCount, participationFee, place) {
        let award = {amount: 0, ticket: 0};
        const minEarnings = ((participantCount * participationFee) -
            UtilityService.calculateAppCommission(participantCount, participationFee) -
            UtilityService.calculateHolderEarnings(participantCount, participationFee)) / 3;
        if (place === 1) {
            award.amount = Number((minEarnings * 2).toFixed(2));
        } else if (place === 2) {
            award.amount = Number(minEarnings.toFixed(2));
        } else if (place === 3) {
            award.ticket = participantCount >= (TYPES.MIN_PARTICIPANT_COUNT + TYPES.MIN_PARTICIPANT_COUNT / 2) ? 2 : 1;
        }
        return award;
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
            return UtilityService.convertToFormat(date, format);
        } else if (format) {
            return UtilityService.convertToFormat(milliseconds, format);
        } else {
            return milliseconds;
        }
    }

    public static dateFromNow(date: string | number | null) {
        const CURRENT_DATE = new Date();
        let result: string = null;
        if (date) {
            const givenDate = new Date(date);
            const difference = CURRENT_DATE.getTime() - givenDate.getTime();
            if (difference <= DATE_TIME.ONE_MINUTE) {
                result = Math.ceil(difference / DATE_TIME.ONE_SECOND) + ' sn önce';
            } else if (difference <= DATE_TIME.ONE_HOUR) {
                result = Math.ceil(difference / DATE_TIME.ONE_MINUTE) + ' dk önce';
            } else if (difference <= DATE_TIME.ONE_DAY) {
                result = Math.ceil(difference / DATE_TIME.ONE_HOUR) + ' saat önce';
            } else if (difference <= DATE_TIME.ONE_WEEK) {
                result = Math.ceil(difference / DATE_TIME.ONE_DAY) + ' gün önce';
            } else {
                result = UtilityService.convertToFormat(givenDate, DATE_TIME.FORMAT.DATE_TIME);
            }
        }
        return result;
    }

    public static validateMerchantForm(form: FormGroup) {
        const controls = form.controls;
        if (controls.merchantType.value === 'PERSONAL') {
            controls.companyTitle.clearValidators();
            controls.taxOffice.clearValidators();
            controls.taxNumber.clearValidators();
            controls.companyTitle.disable();
            controls.taxOffice.disable();
            controls.taxNumber.disable();
        } else {
            controls.companyTitle.setValidators([Validators.required]);
            controls.taxOffice.setValidators([Validators.required]);
            controls.taxNumber.setValidators([Validators.required, Validators.pattern(REGEX.TAX_NUMBER)]);
            controls.companyTitle.enable();
            controls.taxOffice.enable();
            controls.taxNumber.enable();
        }
    }

    private static convertToFormat(date, format = null) {
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
    }

}
