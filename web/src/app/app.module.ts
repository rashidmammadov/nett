import { BrowserModule } from '@angular/platform-browser';
import { Injector, NgModule } from '@angular/core';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './modules/app-routing.module';
import { AppComponent } from './components/app/app.component';
import { AngularMaterialModule } from "./modules/angular-material.module";
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { CookieService } from 'ngx-cookie-service';
import { FlexLayoutModule } from '@angular/flex-layout';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { StoreModule } from '@ngrx/store';
import { OWL_DATE_TIME_LOCALE, OwlDateTimeModule, OwlNativeDateTimeModule, OwlDateTimeIntl } from 'ng-pick-datetime';

import { ActivateComponent } from './components/activate/activate.component';
import { ApplicationComponent } from './components/application/application.component';
import { AttendButtonComponent } from './components/shared/attend-button/attend-button.component';
import { AttendDialogComponent } from './components/shared/attend-dialog/attend-dialog.component';
import { BelongComponent } from './components/belong/belong.component';
import { HomeComponent } from './components/home/home.component';
import { PieChartReportComponent } from './components/shared/pie-chart-report/pie-chart-report.component';
import { LoginComponent } from './components/login/login.component';
import { RankingReportComponent } from './components/shared/ranking-report/ranking-report.component';
import { RegisterComponent } from './components/register/register.component';
import { SearchComponent } from './components/search/search.component';
import { SetMatchScoreDialogComponent } from './components/shared/set-match-score-dialog/set-match-score-dialog.component';
import { SetTournamentComponent } from './components/set-tournament/set-tournament.component';
import { LeaveDialogComponent } from './components/shared/leave-dialog/leave-dialog.component';
import { TimelineReportComponent } from './components/shared/timeline-report/timeline-report.component';
import { TournamentComponent } from './components/tournament/tournament.component';
import { TournamentCardComponent } from './components/shared/tournament-card/tournament-card.component';

import { Cookie } from './services/cookie/cookies.service';
import { ToastService } from './services/toast/toast.service';
import { UtilityService } from './services/utility/utility.service';
import { userReducer } from './store/reducers/user.reducer';

import { progressReducer } from './store/reducers/progress.reducer';
import { TokenInterceptor } from './interceptors/token.interceptor';
import { DatePickerIntl } from './models/DatePickerIntl';
import { NotificationReportComponent } from './components/shared/notification-report/notification-report.component';
import { MostPlayedReportComponent } from './components/shared/most-played-report/most-played-report.component';

@NgModule({
    declarations: [
        ActivateComponent,
        AppComponent,
        ApplicationComponent,
        AttendButtonComponent,
        AttendDialogComponent,
        BelongComponent,
        HomeComponent,
        RegisterComponent,
        SearchComponent,
        SetMatchScoreDialogComponent,
        SetTournamentComponent,
        LeaveDialogComponent,
        LoginComponent,
        TournamentComponent,
        TournamentCardComponent,
        TimelineReportComponent,
        PieChartReportComponent,
        RankingReportComponent,
        NotificationReportComponent,
        MostPlayedReportComponent
    ],
    imports: [
        BrowserModule,
        AppRoutingModule,
        BrowserAnimationsModule,
        AngularMaterialModule,
        FlexLayoutModule,
        FormsModule,
        HttpClientModule,
        ReactiveFormsModule,
        StoreModule.forRoot({progress: progressReducer, user: userReducer}),
        OwlDateTimeModule,
        OwlNativeDateTimeModule,
    ],
    entryComponents: [AttendDialogComponent, LeaveDialogComponent, SetMatchScoreDialogComponent],
    providers: [CookieService,
        { provide: HTTP_INTERCEPTORS, useClass: TokenInterceptor, multi: true},
        { provide: OWL_DATE_TIME_LOCALE, useValue: 'tr'},
        { provide: OwlDateTimeIntl, useClass: DatePickerIntl }
    ],
    bootstrap: [AppComponent]
})
export class AppModule {
    constructor(private injector: Injector) {
        Cookie.injector = injector;
        ToastService.injector = injector;
        UtilityService.injector = injector;
    }
}
