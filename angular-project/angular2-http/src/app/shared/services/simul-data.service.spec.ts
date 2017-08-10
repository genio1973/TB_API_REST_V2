import { TestBed, inject } from '@angular/core/testing';

import { SimulDataService } from './simul-data.service';

describe('SimulDataService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [SimulDataService]
    });
  });

  it('should be created', inject([SimulDataService], (service: SimulDataService) => {
    expect(service).toBeTruthy();
  }));
});
