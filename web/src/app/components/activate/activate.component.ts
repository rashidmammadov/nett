import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { first } from 'rxjs/operators';
import { UserType } from '../../interfaces/user-type';
import { loaded, loading } from '../../store/actions/progress.action';
import { UserService } from '../../services/user/user.service';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { set } from '../../store/actions/user.action';
import { DATE_TIME } from '../../constants/date-time.constant';
import { TYPES } from '../../constants/types.constant';

@Component({
  selector: 'app-activate',
  templateUrl: './activate.component.html',
  styleUrls: ['./activate.component.scss']
})
export class ActivateComponent implements OnInit {
    public currentDate = new Date();
    public days: number[] = [];
    public months: number[] = [];
    public years: number[] = [];
    public selectedDay: number = 1;
    public selectedMonth: number = 1;
    public selectedYear: number = this.currentDate.getFullYear() - 18;
    public MERCHANT_TYPES;
    public MONTHS_MAP = DATE_TIME.MONTHS_MAP;
    private _user: UserType;

    activateForm = new FormGroup({
        name: new FormControl('', [Validators.required]),
        surname: new FormControl('', [Validators.required]),
        identityNumber: new FormControl('', [Validators.required]),
        phone: new FormControl('', [Validators.required]),
        birthday: new FormControl('', [Validators.required]),
        merchantType: new FormControl(''),
        companyTitle: new FormControl('')
    });

    constructor(private store: Store<{user: UserType}>, private progress: Store<{progress: boolean}>,
                private userService: UserService, private router: Router) {
        for (let i = 1; i <= 31; i++) this.days.push(i);
        for (let i = 1; i <= 12; i++) this.months.push(i);
        for (let i = this.currentDate.getFullYear(); i >= this.currentDate.getFullYear() - 70; i--) this.years.push(i);
        this.MERCHANT_TYPES = [];
        Object.keys(TYPES.MERCHANT_TYPES).forEach((key: string) => {
            this.MERCHANT_TYPES.push({name: TYPES.MERCHANT_TYPES[key], value: key});
        });
        this.activateForm.controls.merchantType.setValue(this.MERCHANT_TYPES[0].value);
    }

    ngOnInit() {
        setTimeout(() => this.fetchUser());
    };

    get user(): UserType {
        return this._user;
    }

    set user(value: UserType) {
        this._user = value;
    }

    public activate = async () => {
        if (this.activateForm.valid) {
            this.progress.dispatch(loading());
            const result = await this.userService.activate(this.setActivateFormData());
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                this.user.name = response.data.name;
                this.user.surname = response.data.surname;
                this.user.phone = response.data.phone;
                this.user.birthday = response.data.birthday;
                this.user.state = response.data.state;
                this.store.dispatch(set({user: this.user}));
                this.router.navigateByUrl('app/home');
            });
            this.progress.dispatch(loaded());
        }
    };

    public setBirthday() {
        let birthday = new Date(this.selectedYear, this.selectedMonth - 1, this.selectedDay);
        this.activateForm.controls.birthday.setValue(birthday.getTime().toString());
    }

    private fetchUser = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
        if (this.user.type === TYPES.USER.HOLDER) {
            this.activateForm.controls.merchantType.setValidators([Validators.required]);
            this.activateForm.controls.companyTitle.setValidators([Validators.required]);
        }
    };

    private setActivateFormData() {
        let form = this.activateForm.controls;
        return {
            'name': form.name.value,
            'surname': form.surname.value,
            'identityNumber': form.identityNumber.value.toString(),
            'phone': form.phone.value.toString(),
            'birthday': form.birthday.value
        }
    }

}
