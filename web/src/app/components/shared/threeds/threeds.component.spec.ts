import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ThreedsComponent } from './threeds.component';

describe('ThreedsDialogComponent', () => {
  let component: ThreedsComponent;
  let fixture: ComponentFixture<ThreedsComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ThreedsComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ThreedsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
