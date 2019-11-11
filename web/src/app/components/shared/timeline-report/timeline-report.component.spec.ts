import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TimelineReportComponent } from './timeline-report.component';

describe('TimelineReportComponent', () => {
  let component: TimelineReportComponent;
  let fixture: ComponentFixture<TimelineReportComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TimelineReportComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TimelineReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
