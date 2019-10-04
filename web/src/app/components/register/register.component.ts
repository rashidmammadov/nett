import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { UserType } from '../../interfaces/user-type';
import { REGEX } from '../../constants/regex.constant';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.scss']
})
export class RegisterComponent implements OnInit {
    private _user: UserType;
    public userTypes: object[] = [{value: 'holder', name: 'Holder'}, {value: 'gamer', name: 'Gamer'}];

    registerForm = new FormGroup({
        username: new FormControl('', [Validators.required, Validators.pattern(REGEX.USERNAME)]),
        email: new FormControl('', [Validators.required, Validators.email]),
        type: new FormControl('gamer', [Validators.required]),
        password: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)]),
        passwordConfirmation: new FormControl('', [Validators.required, Validators.pattern(REGEX.PASSWORD)]),
        city: new FormControl('', [Validators.required]),
        district: new FormControl('', [Validators.required])
    });

    constructor() { }

    ngOnInit() {
    }

}
