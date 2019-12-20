import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FinanceArchiveDialogComponent } from './finance-archive-dialog.component';

describe('FinanceArchiveSheetComponent', () => {
  let component: FinanceArchiveDialogComponent;
  let fixture: ComponentFixture<FinanceArchiveDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FinanceArchiveDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FinanceArchiveDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
