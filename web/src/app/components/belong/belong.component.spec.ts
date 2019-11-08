import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { BelongComponent } from './belong.component';

describe('BelongComponent', () => {
  let component: BelongComponent;
  let fixture: ComponentFixture<BelongComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ BelongComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(BelongComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
