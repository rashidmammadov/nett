import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SetMatchScoreDialogComponent } from './set-match-score-dialog.component';

describe('SetMatchScoreDialogComponent', () => {
  let component: SetMatchScoreDialogComponent;
  let fixture: ComponentFixture<SetMatchScoreDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SetMatchScoreDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SetMatchScoreDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
