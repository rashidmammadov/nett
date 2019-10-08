import { ActivatedRouteSnapshot, Resolve, RouterStateSnapshot } from '@angular/router';
import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { IHttpResponse } from '../interfaces/i-http-response';
import { ErrorResponse } from '../models/error-response';
import { GameService } from '../services/game/game.service';

@Injectable()
export class GameResolver implements Resolve<any> {

    constructor(private gameService: GameService) {
    }

    resolve(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<ErrorResponse | IHttpResponse> | Promise<any> | any {
        return this.gameService.get();
    }
}
