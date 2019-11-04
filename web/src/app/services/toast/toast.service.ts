import { Injectable, Injector } from '@angular/core';
import { MatSnackBar } from '@angular/material/snack-bar';

@Injectable({
  providedIn: 'root'
})
export class ToastService {
    public static injector: Injector;

    /**
     * Configuration of toast message.
     */
    private static config: object = {
        horizontalPosition: 'center',
        verticalPosition: 'bottom',
        duration: 2000
    };

    /**
     * Show toast message when triggered.
     * @param {string} message - the message which will be shown as toast message.
     * @param {string} action - the label for the toast action.
     */
    public static show(message: string, action: string = null) {
        this.injector.get(MatSnackBar).open(message, action || 'tamam', this.config);
    }
}
