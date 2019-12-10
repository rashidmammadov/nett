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
        this.isValid(data.gameImage) && HTMLTags.push(`<img src="${data.gameImage}"/>`);
        this.isValid(data.gameName) && HTMLTags.push(`<b>${data.gameName}</b>`);
        this.isValid(data.tournamentRanking) && HTMLTags.push(`Sıralama: <b>${data.tournamentRanking}. yer</b>`);
        this.isValid(data.earnings) && HTMLTags.push(`Kazanç: <b>${data.earnings}</b>`);
        this.isValid(data.participantCount) && HTMLTags.push(`Katılımcı Sayısı: <b>${data.participantCount} kişi</b>`);
        this.isValid(data.startDate) && HTMLTags.push(`Turnuva Tarihi: <b>${UtilityService.millisecondsToDate(data.startDate, DATE_TIME.FORMAT.DATE_TIME)}</b>`);
        return HTMLTags.join('<br/>');
    }

    public static getTooltipXPosition(event, margin, width) {
        let xPosition = event.offsetX;
        if ((width - 190) <= event.offsetX) {
            xPosition = xPosition - (190 + margin.left + margin.right)
        } else {
            xPosition = xPosition + (margin.left + margin.right);
        }
        return `${xPosition}px`;
    }

    public static getTooltipYPosition(event, margin) {
        let yPosition = event.offsetY - (margin.top + margin.bottom);
        return `${yPosition}px`;
    }

    private static isValid(value) {
        return value !== null && value !== undefined;
    }

}
