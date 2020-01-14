/** Modules */
import { AngularMaterialModule } from "./modules/angular-material.module";
import { AppRoutingModule } from './modules/app-routing.module';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { BrowserModule } from '@angular/platform-browser';
import { FlexLayoutModule } from '@angular/flex-layout';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { Injector, NgModule } from '@angular/core';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';
import { StoreModule } from '@ngrx/store';
import { OWL_DATE_TIME_LOCALE, OwlDateTimeModule, OwlNativeDateTimeModule, OwlDateTimeIntl } from 'ng-pick-datetime';
/** Components */
import { ActivateComponent } from './components/activate/activate.component';
import { AppComponent } from './components/app/app.component';
import { ApplicationComponent } from './components/application/application.component';
import { AttendButtonComponent } from './components/shared/attend-button/attend-button.component';
import { AttendDialogComponent } from './components/shared/attend-dialog/attend-dialog.component';
import { BankComponent } from './components/bank/bank.component';
import { BelongComponent } from './components/belong/belong.component';
import { HalfDonutReportComponent } from './components/shared/half-donut-report/half-donut-report.component';
import { HomeComponent } from './components/home/home.component';
import { PieChartReportComponent } from './components/shared/pie-chart-report/pie-chart-report.component';
import { LoginComponent } from './components/login/login.component';
import { MostPlayedReportComponent } from './components/shared/most-played-report/most-played-report.component';
import { NotificationReportComponent } from './components/shared/notification-report/notification-report.component';
import { RankingReportComponent } from './components/shared/ranking-report/ranking-report.component';
import { RegisterComponent } from './components/register/register.component';
import { SearchComponent } from './components/search/search.component';
import { SetTournamentComponent } from './components/set-tournament/set-tournament.component';
import { SettingComponent } from './components/setting/setting.component';
import { LeaveDialogComponent } from './components/shared/leave-dialog/leave-dialog.component';
import { TimelineReportComponent } from './components/shared/timeline-report/timeline-report.component';
import { TournamentComponent } from './components/tournament/tournament.component';
import { TournamentCardComponent } from './components/shared/tournament-card/tournament-card.component';
/** Dialogs */
import { ConfirmDialogComponent } from './components/shared/confirm-dialog/confirm-dialog.component';
import { DepositDialogComponent } from './components/shared/deposit-dialog/deposit-dialog.component';
import { FinanceArchiveDialogComponent } from './components/shared/finance-archive-dialog/finance-archive-dialog.component';
import { ForgotPasswordDialogComponent } from './components/shared/forgot-password-dialog/forgot-password-dialog.component';
import { SetMatchScoreDialogComponent } from './components/shared/set-match-score-dialog/set-match-score-dialog.component';
import { ThreedsDialogComponent } from './components/shared/threeds-dialog/threeds-dialog.component';
import { WithdrawDialogComponent } from './components/shared/withdraw-dialog/withdraw-dialog.component';
/** Services */
import { CookieService } from 'ngx-cookie-service';
import { Cookie } from './services/cookie/cookies.service';
import { ToastService } from './services/toast/toast.service';
import { UtilityService } from './services/utility/utility.service';
import { VisualService } from './services/visual/visual.service';
/** Reducers */
import { userReducer } from './store/reducers/user.reducer';
import { progressReducer } from './store/reducers/progress.reducer';
/** Others */
import { TokenInterceptor } from './interceptors/token.interceptor';
import { DatePickerIntl } from './models/DatePickerIntl';

@NgModule({
    declarations: [
        ActivateComponent,
        AppComponent,
        ApplicationComponent,
        AttendButtonComponent,
        AttendDialogComponent,
        BankComponent,
        BelongComponent,
        ConfirmDialogComponent,
        DepositDialogComponent,
        FinanceArchiveDialogComponent,
        ForgotPasswordDialogComponent,
        HalfDonutReportComponent,
        HomeComponent,
        LeaveDialogComponent,
        LoginComponent,
        MostPlayedReportComponent,
        NotificationReportComponent,
        PieChartReportComponent,
        RankingReportComponent,
        RegisterComponent,
        SearchComponent,
        SetMatchScoreDialogComponent,
        SetTournamentComponent,
        SettingComponent,
        TimelineReportComponent,
        ThreedsDialogComponent,
        TournamentComponent,
        TournamentCardComponent,
        WithdrawDialogComponent
    ],
    imports: [
        AppRoutingModule,
        AngularMaterialModule,
        BrowserAnimationsModule,
        BrowserModule,
        FlexLayoutModule,
        FormsModule,
        HttpClientModule,
        ReactiveFormsModule,
        StoreModule.forRoot({progress: progressReducer, user: userReducer}),
        OwlDateTimeModule,
        OwlNativeDateTimeModule,
    ],
    entryComponents: [
        AttendDialogComponent,
        ConfirmDialogComponent,
        DepositDialogComponent,
        FinanceArchiveDialogComponent,
        ForgotPasswordDialogComponent,
        LeaveDialogComponent,
        SetMatchScoreDialogComponent,
        ThreedsDialogComponent,
        WithdrawDialogComponent
    ],
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
        VisualService.injector = injector;
    }
}
