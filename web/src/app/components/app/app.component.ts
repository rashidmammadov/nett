import { Component } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { MatIconRegistry } from '@angular/material';
import { Router } from '@angular/router';
import { select, Store } from '@ngrx/store';
import { set } from '../../store/actions/user.action';
import { UserType } from '../../interfaces/user-type';
import { UserService } from '../../services/user/user.service';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { Cookie } from '../../services/cookie/cookies.service';
import { ToastService } from '../../services/toast/toast.service';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
    progress: boolean;
    user: UserType;

    constructor(private domSanitizer: DomSanitizer, public matIconRegistry: MatIconRegistry,
                private store: Store<{progress: boolean, user: UserType}>, private userService: UserService,
                private router: Router) {
        store.pipe(select('user')).subscribe(data => {
            setTimeout(() => this.user = data, 0);
        });
        store.pipe(select('progress')).subscribe(data => {
            setTimeout(() => this.progress = data, 0);
        });
        this.setSvgIcons();
    }

    logout = async () => {
        this.progress = true;
        const result = await this.userService.logout();
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            Cookie.delete('_nrt');
            this.store.dispatch(set({user: null}));
            ToastService.show(response.message);
            this.router.navigateByUrl('login');
        });
        this.progress = false;
    };

    private setSvgIcons() {
        const svgArray: string[] = ['alert', 'briefcase', 'clock', 'credit-card', 'dollar-sign', 'flag', 'grid', 'hash',
            'home', 'info', 'log-out', 'plus-square', 'ranking-down', 'ranking-new', 'ranking-not', 'ranking-stable',
            'ranking-up', 'search', 'tag', 'tournament-active', 'tournament-cancel', 'tournament-close', 'tournament-open',
            'user', 'users'];
        let path: string = 'assets/icons/';
        svgArray.forEach((svg: string) => {
            this.matIconRegistry.addSvgIcon(svg, this.domSanitizer.bypassSecurityTrustResourceUrl(path + svg + '.svg'));
        });
    }

}
