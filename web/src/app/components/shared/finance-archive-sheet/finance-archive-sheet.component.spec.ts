import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FinanceArchiveSheetComponent } from './finance-archive-sheet.component';

describe('FinanceArchiveSheetComponent', () => {
  let component: FinanceArchiveSheetComponent;
  let fixture: ComponentFixture<FinanceArchiveSheetComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FinanceArchiveSheetComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FinanceArchiveSheetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
