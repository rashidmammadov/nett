import { Injectable } from '@angular/core';
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot, UrlTree } from '@angular/router';
import { Store } from '@ngrx/store';
import { UserService } from '../services/user/user.service';
import { Cookie } from '../services/cookie/cookies.service';
import { UserType } from '../interfaces/user-type';
import { UtilityService } from '../services/utility/utility.service';
import { IHttpResponse } from '../interfaces/i-http-response';
import { set } from '../store/actions/user.action';

@Injectable({
  providedIn: 'root'
})
export class AuthGuard implements CanActivate {

    constructor(private userService: UserService, private router: Router, private user: Store<{user: UserType}>) {
    }

    canActivate = async (
        next: ActivatedRouteSnapshot,
        state: RouterStateSnapshot) => {
        const accessToken = Cookie.get('_nrt');
        const securedState = state.url.indexOf('app') !== -1;
        const activateState = state.url.indexOf('activate') !== -1;
        if (accessToken) {
            const result = await this.userService.refreshUser();
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                let user: UserType = response.data;
                Cookie.set('_nrt', user.remember_token);
                this.user.dispatch(set({user: user}));
                if (accessToken && Number(user.state) === 1 && activateState) {
                    this.router.navigateByUrl('app/home');
                    return false;
                } else if (accessToken && Number(user.state) === 0 && !activateState) {
                    this.router.navigateByUrl('activate');
                    return false;
                }
            });
        } else if (!accessToken && securedState) {
            this.router.navigateByUrl('login');
            return false;
        }
        return true;
    }
}
