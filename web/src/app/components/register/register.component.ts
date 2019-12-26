import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Router } from '@angular/router';
import { Store } from '@ngrx/store';
import { REGEX } from '../../constants/regex.constant';
import { Cookie } from '../../services/cookie/cookies.service';
import { DataService } from '../../services/data/data.service';
import { UtilityService } from '../../services/utility/utility.service';
import { UserService } from '../../services/user/user.service';
import { UserType } from '../../interfaces/user-type';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { loaded, loading } from '../../store/actions/progress.action';
import { set } from '../../store/actions/user.action';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {
    private _user: UserType;
    public regions: object[] = [];
    public cities: string[] = [];
    public districts: any;
    public userTypes: object[] = [{value: 'holder', name: 'Turnuva Düzenlemek İstiyorum'}, {value: 'player', name: 'Yarışmak İstiyorum'}];

    registerForm = new FormGroup({
        username: new FormControl('', [Validators.required, Validators.pattern(REGEX.USERNAME)]),
        email: new FormControl('', [Validators.required, Validators.email]),
        type: new FormControl('player', [Validators.required]),
        password: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)]),
        passwordConfirmation: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)]),
        city: new FormControl('', [Validators.required]),
        district: new FormControl('', [Validators.required])
    });

    constructor(private dataService: DataService, private progress: Store<{progress: boolean}>,
                private user: Store<{user: UserType}>, private userService: UserService, private router: Router) { }

    ngOnInit() {
        setTimeout(() => this.getRegions());
    }

    public register = async () => {
        if (this.registerForm.valid) {
            this.progress.dispatch(loading());
            const result = await this.userService.register(this.setRegisterFormData());
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                this._user = response.data;
                Cookie.set('_nrt', this._user.remember_token);
                this.user.dispatch(set({user: this._user}));
                this.router.navigateByUrl('activate');
            });
            this.progress.dispatch(loaded());
        }
    };

    public setDistricts(city) {
        this.districts = this.regions[city];
        this.registerForm.controls.district.setValue(this.districts[0]);
    }

    private getRegions = async () => {
        this.progress.dispatch(loading());
        const result = await this.dataService.getRegions();
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.regions = response.data.regions;
            this.cities = Object.keys(response.data.regions);
            this.registerForm.controls.city.setValue(this.cities[34]);
            this.setDistricts(this.cities[34]);
        });
        this.progress.dispatch(loaded());
    };

    private setRegisterFormData() {
        let form = this.registerForm.controls;
        return {
            'username': form.username.value,
            'email': form.email.value,
            'type': form.type.value,
            'password': form.password.value,
            'passwordConfirmation': form.passwordConfirmation.value,
            'city': form.city.value,
            'district': form.district.value
        }
    }

}
