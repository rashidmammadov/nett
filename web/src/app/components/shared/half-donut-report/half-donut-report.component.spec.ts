import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { HalfDonutReportComponent } from './half-donut-report.component';

describe('HalfDonutReportComponent', () => {
  let component: HalfDonutReportComponent;
  let fixture: ComponentFixture<HalfDonutReportComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ HalfDonutReportComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(HalfDonutReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
