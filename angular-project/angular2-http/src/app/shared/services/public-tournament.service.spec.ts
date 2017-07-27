import { TestBed, inject } from '@angular/core/testing';

import { PublicTournamentService } from './public-tournament.service';

describe('PublicTournamentService', () => {
  beforeEach(() => {
    TestBed.configureTestingModule({
      providers: [PublicTournamentService]
    });
  });

  it('should be created', inject([PublicTournamentService], (service: PublicTournamentService) => {
    expect(service).toBeTruthy();
  }));
});
