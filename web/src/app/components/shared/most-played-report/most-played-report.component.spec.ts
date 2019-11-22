import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MostPlayedReportComponent } from './most-played-report.component';

describe('MostPlayedReportComponent', () => {
  let component: MostPlayedReportComponent;
  let fixture: ComponentFixture<MostPlayedReportComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MostPlayedReportComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MostPlayedReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
