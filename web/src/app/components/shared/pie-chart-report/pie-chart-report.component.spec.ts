import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { PieChartReportComponent } from './pie-chart-report.component';

describe('PieChartReportComponent', () => {
  let component: PieChartReportComponent;
  let fixture: ComponentFixture<PieChartReportComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ PieChartReportComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(PieChartReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
