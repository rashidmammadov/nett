import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SetTournamentComponent } from './set-tournament.component';

describe('SetTournamentComponent', () => {
  let component: SetTournamentComponent;
  let fixture: ComponentFixture<SetTournamentComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SetTournamentComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SetTournamentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
