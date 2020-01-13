import { Component, Input, OnInit, ViewChild, ElementRef } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';

@Component({
  selector: 'app-threeds',
  templateUrl: './threeds.component.html',
  styleUrls: ['./threeds.component.scss']
})
export class ThreedsComponent implements OnInit {
    @Input() public data: string;
    @ViewChild('iframe', { static: true }) iframe: ElementRef;
    trustedHTML: any;

    constructor(private sanitizer: DomSanitizer) { }

    ngOnInit(): void {
        this.trustedHTML = this.sanitizer.bypassSecurityTrustHtml(this.data);
        setTimeout(() => this.setIframe(this.iframe));
    }

    private setIframe(iframe: ElementRef): void {
        const win: Window = iframe.nativeElement.contentWindow;
        const doc: Document = win.document;
        doc.open();
        doc.write(this.data);
        doc.close()
    }

}
