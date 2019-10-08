import { BrowserModule } from '@angular/platform-browser';
import { Injector, NgModule } from '@angular/core';
import { HTTP_INTERCEPTORS, HttpClientModule } from '@angular/common/http';

import { AppRoutingModule } from './modules/app-routing.module';
import { AppComponent } from './components/app/app.component';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AngularMaterialModule } from "./modules/angular-material.module";
import { FlexLayoutModule } from '@angular/flex-layout';
import { FormsModule, ReactiveFormsModule } from '@angular/forms';
import { StoreModule } from '@ngrx/store';

import { ActivateComponent } from './components/activate/activate.component';
import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';

import { CookieService } from 'ngx-cookie-service';
import { Cookie } from './services/cookie/cookies.service';

import { userReducer } from './store/reducers/user.reducer';
import { progressReducer } from './store/reducers/progress.reducer';

import { TokenInterceptor } from "./interceptors/token.interceptor";
import { ApplicationComponent } from './components/application/application.component';
import { SearchComponent } from './components/search/search.component';
import { SetTournamentComponent } from './components/set-tournament/set-tournament.component';
import { TournamentCardComponent } from './components/shared/tournament-card/tournament-card.component';

@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    ActivateComponent,
    HomeComponent,
    ApplicationComponent,
    SearchComponent,
    SetTournamentComponent,
    TournamentCardComponent
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
    StoreModule.forRoot({progress: progressReducer, user: userReducer})
  ],
  providers: [CookieService,
    {
      provide: HTTP_INTERCEPTORS,
      useClass: TokenInterceptor,
      multi: true
    }
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
  constructor(private injector: Injector) {
    Cookie.injector = injector;
  }
}
