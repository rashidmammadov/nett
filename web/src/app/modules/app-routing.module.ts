import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { ActivateComponent } from '../components/activate/activate.component';
import { HomeComponent } from '../components/home/home.component';
import { LoginComponent } from '../components/login/login.component';
import { RegisterComponent } from '../components/register/register.component';
import { AuthGuard } from '../guards/auth.guard';
import { ApplicationComponent } from '../components/application/application.component';
import { SearchComponent } from '../components/search/search.component';
import { SetTournamentComponent } from '../components/set-tournament/set-tournament.component';
import { GameResolver } from '../resolvers/game.resolver';

const routes: Routes = [
  {
      path: '',
      pathMatch: 'full',
      redirectTo: 'login',
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
              path: 'search',
              component: SearchComponent
          },
          {
              path: 'set-tournament',
              component: SetTournamentComponent,
              resolve: {games: GameResolver}
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
  providers: [GameResolver]
})
export class AppRoutingModule { }
