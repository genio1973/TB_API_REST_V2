import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TournamentSingleComponent } from './tournament-single.component';

describe('TournamentSingleComponent', () => {
  let component: TournamentSingleComponent;
  let fixture: ComponentFixture<TournamentSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TournamentSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TournamentSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
