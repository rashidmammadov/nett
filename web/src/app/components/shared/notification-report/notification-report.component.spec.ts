import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { NotificationReportComponent } from './notification-report.component';

describe('NotificationReportComponent', () => {
  let component: NotificationReportComponent;
  let fixture: ComponentFixture<NotificationReportComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ NotificationReportComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(NotificationReportComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
