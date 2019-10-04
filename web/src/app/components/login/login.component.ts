import { Component, OnInit } from '@angular/core';
import { Store } from '@ngrx/store';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { UserService } from '../../services/user/user.service';
import { UtilityService } from '../../services/utility/utility.service';
import { IHttpResponse } from '../../interfaces/i-http-response';
import { Cookie } from '../../services/cookie/cookies.service';
import { UserType } from '../../interfaces/user-type';
import { set } from '../../store/actions/user.action';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
    private _user: UserType;

    loginForm = new FormGroup({
        email: new FormControl('', [Validators.required, Validators.email]),
        password: new FormControl('', [Validators.required])
    });

    constructor(private userService: UserService, private user: Store<{user: UserType}>) { }

    public login = async () => {
        if (this.loginForm.valid) {
            const result = await this.userService.login(this.loginForm.controls.email.value, this.loginForm.controls.password.value);
            UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
                this._user = response.data;
                Cookie.set('_nrt', this._user.remember_token);
                this.user.dispatch(set({user: this._user}));
            });
        }
    };

    ngOnInit() {}

}
