import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AttendDialogComponent } from './attend-dialog.component';

describe('AttendDialogComponent', () => {
  let component: AttendDialogComponent;
  let fixture: ComponentFixture<AttendDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AttendDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AttendDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
