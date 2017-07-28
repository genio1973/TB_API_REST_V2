import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TournamentDeleteComponent } from './tournament-delete.component';

describe('TournamentDeleteComponent', () => {
  let component: TournamentDeleteComponent;
  let fixture: ComponentFixture<TournamentDeleteComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TournamentDeleteComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TournamentDeleteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
