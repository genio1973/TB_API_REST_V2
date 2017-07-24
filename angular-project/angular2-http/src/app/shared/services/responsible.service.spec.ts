import { TestBed, inject } from '@angular/core/testing';

import { ResponsibleService } from './responsible.service';

describe('ResponsibleService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [ResponsibleService]
    });
  });

  it('should be created', inject([ResponsibleService], (service: ResponsibleService) => {
    expect(service).toBeTruthy();
  }));
});
