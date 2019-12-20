import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { MatDialog } from '@angular/material/dialog';
import { first } from 'rxjs/operators';
import { GameType } from '../../interfaces/game-type';
import { TournamentType } from '../../interfaces/tournament-type';
import { UserType } from '../../interfaces/user-type';
import { TournamentService } from '../../services/tournament/tournament.service';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { loaded, loading } from '../../store/actions/progress.action';
import { TYPES } from '../../constants/types.constant';
import { ToastService } from '../../services/toast/toast.service';
import { ConfirmDialogComponent } from '../shared/confirm-dialog/confirm-dialog.component';

@Component({
  selector: 'app-set-tournament',
  templateUrl: './set-tournament.component.html',
  styleUrls: ['./set-tournament.component.scss']
})
export class SetTournamentComponent implements OnInit {
    public participantList: number[] = [];
    public feeList: number[] = [];
    public games: GameType[] = [];
    public types: string[] = [];
    public tournamentData: TournamentType = <TournamentType>Object();
    public income: number = 0.00;
    public firstPlaceAward = {amount: 0, ticket: 0};
    public secondPlaceAward = {amount: 0, ticket: 0};
    public thirdPlaceAward = {amount: 0, ticket: 0};
    public tournamentTypesMap = TYPES.TOURNAMENT_TYPE;

    date = new Date();
    minDate = new Date(this.date.getFullYear(), this.date.getMonth(), this.date.getDate() + 7, 12, 0, 0);
    maxDate = new Date(this.date.getFullYear(), this.date.getMonth() + 4, this.date.getDate(), 12, 0, 0);

    tournamentForm = new FormGroup({
        gameId: new FormControl(1, [Validators.required]),
        tournamentType: new FormControl('knock_out', [Validators.required]),
        days: new FormControl(1, [Validators.required]),
        participantCount: new FormControl(TYPES.MIN_PARTICIPANT_COUNT,
            [Validators.min(TYPES.MIN_PARTICIPANT_COUNT), Validators.max(TYPES.MAX_PARTICIPANT_COUNT)]),
        participationFee: new FormControl(TYPES.MIN_PARTICIPATION_FEE,
            [Validators.min(TYPES.MIN_PARTICIPATION_FEE), Validators.max(TYPES.MAX_PARTICIPATION_FEE)]),
        startDate: new FormControl(this.minDate, [Validators.required])
    });

    constructor(private activatedRoute: ActivatedRoute, private user: Store<{user: UserType}>, private router: Router,
                private tournamentService: TournamentService, private progress: Store<{progress: boolean}>,
                private dialog: MatDialog) {
        for (let i = 16; i <= 32; i++) this.participantList.push(i);
        for (let i = 15; i <= 20; i++) this.feeList.push(i);
    }

    async ngOnInit() {
        this.games = (await this.activatedRoute.data.pipe(first()).toPromise()).games.data as GameType[];
        this.games && this.games.length && (this.tournamentData.game = this.games[0]);
        this.tournamentData.holder = await this.user.select('user').pipe(first()).toPromise();
        this.changeTournamentDate();
        this.changeTournamentGame();
        this.changeTournamentData();
    }

    public changeTournamentDate() {
        let selectedDate: Date = this.tournamentForm.controls.startDate.value;
        let convert = (value) => value < 10 ? '0' + value : value;

        if (selectedDate) {
            this.tournamentData.date = convert(selectedDate.getDate()) + '/' + convert(selectedDate.getMonth() + 1);
            this.tournamentData.time = convert(selectedDate.getHours()) + ':' + convert(selectedDate.getMinutes());
        }
    }

    public changeTournamentGame() {
        const selectedGameId = this.tournamentForm.controls.gameId.value;
        const game = this.games.find((game: GameType) => Number(game.gameId) === Number(selectedGameId));
        this.types = game.playingType;
        this.tournamentForm.controls.tournamentType.setValue(this.types[0]);
        this.tournamentData.game = game;
    }

    public changeTournamentData() {
        const participantCount = this.tournamentForm.controls.participantCount.value;
        const participationFee = this.tournamentForm.controls.participationFee.value;
        this.tournamentData.participantCount = participantCount;
        this.tournamentData.participationFee = participationFee;
        this.income = UtilityService.calculateHolderEarnings(participantCount, participationFee);
        this.firstPlaceAward = UtilityService.calculateWinnersEarnings(participantCount, participationFee, 1);
        this.secondPlaceAward = UtilityService.calculateWinnersEarnings(participantCount, participationFee, 2);
        this.thirdPlaceAward = UtilityService.calculateWinnersEarnings(participantCount, participationFee, 3);
    }

    public submitDialog() {
        this.dialog.open(ConfirmDialogComponent, {
            width: window.innerWidth >= 960 ? '40%' : '90%',
            data: {
                title: 'Dikkat',
                body: 'Turnuvaya oluşturduğunuz anda yayımlanacaktır. Eğer yeterli katılımcı sayısına ulaşılamazsa ' +
                  'turnuva otomatik iptal edilecek ve o ana kadar katılan tüm kullanıcıların katılım ücretleri iade edilecektir.'
            }
        }).afterClosed().toPromise().then((result) => {
            !!result && this.submit();
        });
    }

    private submit = async () => {
        if (this.tournamentForm.valid) {
            this.progress.dispatch(loading());
            const result = await this.tournamentService.add(this.setTournamentFormData());
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                ToastService.show(response.message);
                this.router.navigateByUrl(`app/tournament/${response.data.tournamentId}`);
            });
            this.progress.dispatch(loaded());
        }
    };

    private setTournamentFormData() {
        let form = this.tournamentForm.controls;
        return {
            'gameId': form.gameId.value,
            'tournamentType': form.tournamentType.value,
            'days': form.days.value,
            'participantCount': form.participantCount.value,
            'participationFee': form.participationFee.value,
            'startDate': new Date(form.startDate.value).getTime()
        }
    }
}
