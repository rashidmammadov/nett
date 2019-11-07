import { Component } from '@angular/core';
import { Observable } from 'rxjs/internal/Observable';
import { DomSanitizer } from '@angular/platform-browser';
import { MatIconRegistry } from '@angular/material';
import { select, Store } from '@ngrx/store';
import { UserType } from '../../interfaces/user-type';

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss']
})
export class AppComponent {
    progress: boolean;
    user: Observable<UserType>;

    constructor(private domSanitizer: DomSanitizer, public matIconRegistry: MatIconRegistry,
                private store: Store<{progress: boolean, user: UserType}>) {
        this.user = this.store.pipe(select('user'));
        store.pipe(select('progress')).subscribe(data => {
            setTimeout(() => this.progress = data, 0);
        });
        this.setSvgIcons();
    }

    private setSvgIcons() {
        const svgArray: string[] = ['briefcase', 'clock', 'credit-card', 'dollar-sign', 'flag', 'grid', 'hash', 'home',
          'info', 'plus-square', 'search', 'tag', 'user', 'users'];
        let path: string = 'assets/icons/';
        svgArray.forEach((svg: string) => {
            this.matIconRegistry.addSvgIcon(svg, this.domSanitizer.bypassSecurityTrustResourceUrl(path + svg + '.svg'));
        });
    }

}
