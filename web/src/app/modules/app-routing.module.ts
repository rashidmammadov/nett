import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { ActivateComponent } from '../components/activate/activate.component';
import { ApplicationComponent } from '../components/application/application.component';
import { AuthGuard } from '../guards/auth.guard';
import { BankComponent } from '../components/bank/bank.component';
import { BelongComponent } from '../components/belong/belong.component';
import { HomeComponent } from '../components/home/home.component';
import { LandingComponent } from '../components/landing/landing.component';
import { LoginComponent } from '../components/login/login.component';
import { RegisterComponent } from '../components/register/register.component';
import { SearchComponent } from '../components/search/search.component';
import { SetTournamentComponent } from '../components/set-tournament/set-tournament.component';

import { SettingComponent } from '../components/setting/setting.component';
import { TournamentComponent } from '../components/tournament/tournament.component';
import { GameResolver } from '../resolvers/game.resolver';
import { TournamentResolver } from '../resolvers/tournament.resolver';

const routes: Routes = [
  {
      path: '',
      pathMatch: 'full',
      // redirectTo: 'login',
      component: LandingComponent,
      canActivate: [AuthGuard]
  },
  {
      path: 'login',
      component: LoginComponent,
      canActivate: [AuthGuard]
  },
  {
      path: 'register',
      component: RegisterComponent,
      canActivate: [AuthGuard]
  },
  {
      path: 'activate',
      component: ActivateComponent,
      canActivate: [AuthGuard]
  },
  {
      path: 'app',
      canActivate: [AuthGuard],
      component: ApplicationComponent,
      children: [
          {
              path: 'home',
              component: HomeComponent
          },
          {
              path: 'bank',
              component: BankComponent
          },
          {
              path: 'belongs',
              component: BelongComponent
          },
          {
              path: 'search',
              component: SearchComponent
          },
          {
              path: 'set-tournament',
              component: SetTournamentComponent,
              resolve: {games: GameResolver}
          },
          {
              path: 'tournament/:tournamentId',
              component: TournamentComponent,
              resolve: {tournament: TournamentResolver}
          },
          {
              path: 'settings',
              component: SettingComponent
          },
          {
              path: '',
              pathMatch: 'full',
              redirectTo: 'home'
          }
      ]
  }
];

@NgModule({
    imports: [RouterModule.forRoot(routes)],
    exports: [RouterModule],
    providers: [GameResolver, TournamentResolver]
})
export class AppRoutingModule { }
