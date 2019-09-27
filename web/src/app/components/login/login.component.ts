import { Component, OnInit } from '@angular/core';
import { UserService } from "../../services/user/user.service";
import { UtilityService } from "../../services/utility/utility.service";
import { IHttpResponse } from "../../interfaces/i-http-response";

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.scss']
})
export class LoginComponent implements OnInit {
  email: string;
  password: string | number;

  constructor(private userService: UserService) { }

  public login = async () => {
    const result = await this.userService.login(this.email, this.password);
    UtilityService.handleResponseFromService(result, (response: IHttpResponse) => {
      console.log(response.data);
    });
  };

  ngOnInit() {}

}
