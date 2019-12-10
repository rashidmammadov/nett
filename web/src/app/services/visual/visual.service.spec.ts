import { TestBed } from '@angular/core/testing';

import { VisualService } from './visual.service';

describe('VisualService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: VisualService = TestBed.get(VisualService);
    expect(service).toBeTruthy();
  });
});
