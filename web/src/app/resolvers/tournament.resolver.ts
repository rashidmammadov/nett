import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { IHttpResponse } from '../interfaces/i-http-response';
import { ErrorResponse } from '../models/error-response';
import { TournamentService } from '../services/tournament/tournament.service';

@Injectable()
export class TournamentResolver implements Resolve<any> {

    constructor(private tournamentService: TournamentService) { }

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<ErrorResponse | IHttpResponse> | Promise<any> | any {
        const tournamentId: number = route.params.tournamentId;
        return this.tournamentService.get(Number(tournamentId));
    }
}
