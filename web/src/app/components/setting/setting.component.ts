import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Store } from '@ngrx/store';
import { Router } from '@angular/router';
import { first } from 'rxjs/operators';
import { UserType } from '../../interfaces/user-type';
import { loaded, loading } from '../../store/actions/progress.action';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { DataService } from '../../services/data/data.service';
import { REGEX } from '../../constants/regex.constant';
import { set } from '../../store/actions/user.action';
import { UserService } from '../../services/user/user.service';
import { ToastService } from '../../services/toast/toast.service';
import { Cookie } from '../../services/cookie/cookies.service';

@Component({
  selector: 'app-setting',
  templateUrl: './setting.component.html',
  styleUrls: ['./setting.component.scss']
})
export class SettingComponent implements OnInit {
    user: UserType;
    public regions: object[] = [];
    public cities: string[] = [];
    public districts: any;

    passwordForm = new FormGroup({
        password: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)]),
        passwordConfirmation: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)])
    });

    settingsForm = new FormGroup({
        username: new FormControl('', [Validators.required]),
        email: new FormControl('', [Validators.required, Validators.email]),
        name: new FormControl('', [Validators.required]),
        surname: new FormControl('', [Validators.required]),
        city: new FormControl('', [Validators.required]),
        district: new FormControl('', [Validators.required]),
        phone: new FormControl('', [Validators.required]),
        iban: new FormControl(''),
        address: new FormControl('')
    });

    constructor(private store: Store<{user: UserType, progress: boolean}>, private dataService: DataService,
                private userService: UserService, private router: Router) {}

    ngOnInit() {
        this.getRegions().then(this.getUserData);
    }

    public updatePassword = async () => {
        if (this.passwordForm.valid) {
            this.store.dispatch(loading());
            const result = await this.userService.updatePassword(this.setPasswordFormData());
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                Cookie.delete('_nrt');
                this.store.dispatch(set({user: null}));
                ToastService.show(response.message);
                this.router.navigateByUrl('login');
            });
            this.store.dispatch(loaded());
        }
    };

    public updateSettings = async () => {
        if (this.settingsForm.valid) {
            this.store.dispatch(loading());
            const result = await this.userService.updateSettings(this.setSettingsFormData());
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                this.user = response.data;
                this.store.dispatch(set({user: this.user}));
                ToastService.show(response.message);
            });
            this.store.dispatch(loaded());
        }
    };

    public setDistricts(city, change = true) {
        this.districts = this.regions[city];
        change && this.settingsForm.controls.district.setValue(this.districts[0]);
    }

    private getRegions = async () => {
        this.store.dispatch(loading());
        const result = await this.dataService.get(true, false);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.regions = response.data.regions;
            this.cities = Object.keys(response.data.regions);
        });
        this.store.dispatch(loaded());
    };

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
        this.initSettingsFormData();
    };

    private initSettingsFormData() {
        let form = this.settingsForm.controls;
        form.email.disable();
        form.username.disable();
        Object.keys(form).forEach((key: string) => { form[key].setValue(this.user[key]); });
        this.setDistricts(form.city.value, false);
    }

    private setPasswordFormData() {
        let form = this.passwordForm.controls;
        return {
            'password': form.password.value,
            'passwordConfirmation': form.passwordConfirmation.value
        }
    }

    private setSettingsFormData() {
        let form = this.settingsForm.controls;
        return {
            'name': form.name.value,
            'surname': form.surname.value,
            'city': form.city.value,
            'district': form.district.value,
            'phone': form.phone.value,
            'iban': form.iban.value,
            'address': form.address.value,
        }
    }

}
