import { Injectable, Injector } from '@angular/core';
import { UtilityService } from '../utility/utility.service';
import { DATE_TIME } from '../../constants/date-time.constant';

@Injectable({
  providedIn: 'root'
})
export class VisualService {

    public static injector: Injector;

    public static prepareTooltipHTML(data) {
        let HTMLTags = [];
        HTMLTags.push(`<img src="${data.gameImage}"/>`);
        HTMLTags.push(`<b>${data.gameName}</b>`);
        HTMLTags.push(`Kazanç: <b>${data.earnings}</b>`);
        HTMLTags.push(`Katılımcı Sayısı: <b>${data.participantCount} kişi</b>`);
        HTMLTags.push(`Turnuva Tarihi: <b>${UtilityService.millisecondsToDate(data.startDate, DATE_TIME.FORMAT.DATE_TIME)}</b>`);
        return HTMLTags.join('<br/>');
    }

}
