import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { TeamSingleComponent } from './team-single.component';

describe('TeamSingleComponent', () => {
  let component: TeamSingleComponent;
  let fixture: ComponentFixture<TeamSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ TeamSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(TeamSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
