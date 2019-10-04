import { TestBed } from '@angular/core/testing';

import { Cookie } from './cookies.service';

describe('CookieService', () => {
  beforeEach(() => TestBed.configureTestingModule({}));

  it('should be created', () => {
    const service: Cookie = TestBed.get(Cookie);
    expect(service).toBeTruthy();
  });
});
