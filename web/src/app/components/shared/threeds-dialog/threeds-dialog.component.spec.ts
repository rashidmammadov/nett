import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ThreedsDialogComponent } from './threeds-dialog.component';

describe('ThreedsDialogComponent', () => {
  let component: ThreedsDialogComponent;
  let fixture: ComponentFixture<ThreedsDialogComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ThreedsDialogComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ThreedsDialogComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
