import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { Store } from '@ngrx/store';
import { Router } from '@angular/router';
import {first, map, startWith} from 'rxjs/operators';
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
import { TYPES } from '../../constants/types.constant';
import { TaxOfficeType } from '../../interfaces/tax-office-type';
import { Observable } from 'rxjs';

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
    public taxOffices: TaxOfficeType[] = [];
    public filteredTaxOffices: Observable<TaxOfficeType[]>;
    public MERCHANT_TYPES = [];

    passwordForm: FormGroup = new FormGroup({
        password: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)]),
        passwordConfirmation: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)])
    });
    merchantForm: FormGroup;
    settingsForm: FormGroup;

    constructor(private store: Store<{user: UserType, progress: boolean}>, private dataService: DataService,
                private userService: UserService, private router: Router) {
        Object.keys(TYPES.MERCHANT_TYPES).forEach((key: string) => {
            this.MERCHANT_TYPES.push({name: TYPES.MERCHANT_TYPES[key], value: key});
        });
    }

    ngOnInit() {
        this.getRegionsAndTaxOffices().then(this.getUserData);
    }

    public isHolder() {
        return this.user.type === TYPES.USER.HOLDER;
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

    public updateMerchant = async () => {
        if (this.merchantForm.valid) {
            this.store.dispatch(loading());
            const result = await this.userService.updateMerchant(this.setMerchantFormData());
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                this.user.merchant = response.data;
                this.store.dispatch(set({user: this.user}));
                ToastService.show(response.message);
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

    private getRegionsAndTaxOffices = async () => {
        this.store.dispatch(loading());
        const result = await this.dataService.get(true, true);
        UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
            this.regions = response.data.regions;
            this.cities = Object.keys(response.data.regions);
            this.taxOffices = response.data.taxOffices;
        });
        this.store.dispatch(loaded());
    };

    private getUserData = async () => {
        this.user = await this.store.select('user').pipe(first()).toPromise();
        this.initMerchantFormData();
        this.initSettingsFormData();
    };

    private filterTaxOffice(value: string): TaxOfficeType[] {
        const filterValue = value.toLowerCase();
        return this.taxOffices.filter((to: TaxOfficeType) => to.taxOffice.toLowerCase().indexOf(filterValue) === 0);
    }

    private initMerchantFormData() {
        const merchant = this.user.merchant;
        this.merchantForm = new FormGroup({
            iban: new FormControl(merchant.iban, [Validators.required]),
            identityNumber: new FormControl(merchant.identityNumber, [Validators.required]),
            merchantType: new FormControl(merchant.merchantType, [Validators.required]),
            taxOffice: new FormControl(merchant.taxOffice, this.isHolder() ? [Validators.required] : null),
            taxNumber: new FormControl(merchant.taxOffice, this.isHolder() ? [Validators.required] : null),
            companyTitle: new FormControl(merchant.companyTitle, this.isHolder() ? [Validators.required] : null)
        });
        this.filteredTaxOffices = this.merchantForm.controls.taxOffice.valueChanges.pipe(
            startWith(''),
            map((value: any) => this.filterTaxOffice(value))
        );
    }

    private initSettingsFormData() {
        const user = this.user;
        this.settingsForm = new FormGroup({
            username: new FormControl(user.username, [Validators.required]),
            email: new FormControl(user.email, [Validators.required, Validators.email]),
            name: new FormControl(user.name, [Validators.required]),
            surname: new FormControl(user.surname, [Validators.required]),
            city: new FormControl(user.city, [Validators.required]),
            district: new FormControl(user.district, [Validators.required]),
            phone: new FormControl(user.phone, [Validators.required]),
            address: new FormControl(user.address, [Validators.required])
        });
        let form = this.settingsForm.controls;
        form.email.disable();
        form.username.disable();
        this.setDistricts(form.city.value, false);
    }

    private setPasswordFormData() {
        let form = this.passwordForm.controls;
        return {
            'password': form.password.value,
            'passwordConfirmation': form.passwordConfirmation.value
        }
    }

    private setMerchantFormData() {
        let form = this.merchantForm.controls;
        return {
            'iban': form.iban.value,
            'identityNumber': form.identityNumber.value,
            'merchantType': form.merchantType.value,
            'taxOffice': form.taxOffice.value,
            'taxNumber': form.taxNumber.value,
            'companyTitle': form.companyTitle.value
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
