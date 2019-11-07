import { Injectable, Injector } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';

@Injectable({
  providedIn: 'root'
})
export class Cookie {
    public static injector: Injector;

    public static set(key: string, value: string): void {
        Cookie.injector.get(CookieService).set(key, value);
    }

    public static get(key: string): string {
        return Cookie.injector.get(CookieService).get(key);
    }

    public static delete(key: string): void {
        Cookie.injector.get(CookieService).delete(key);
    }

}
