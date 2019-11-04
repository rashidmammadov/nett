import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AttendButtonComponent } from './attend-button.component';

describe('AttendButtonComponent', () => {
  let component: AttendButtonComponent;
  let fixture: ComponentFixture<AttendButtonComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AttendButtonComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AttendButtonComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
