import { Component, OnInit, ViewChild, ElementRef, Inject } from '@angular/core';
import { DomSanitizer } from '@angular/platform-browser';
import { MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-threeds-dialog',
  templateUrl: './threeds-dialog.component.html',
  styleUrls: ['./threeds-dialog.component.scss']
})
export class ThreedsDialogComponent implements OnInit {
    @ViewChild('iframe', { static: true }) iframe: ElementRef;
    trustedHTML: any;

    constructor(private sanitizer: DomSanitizer, @Inject(MAT_DIALOG_DATA) public data: string) { }

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
