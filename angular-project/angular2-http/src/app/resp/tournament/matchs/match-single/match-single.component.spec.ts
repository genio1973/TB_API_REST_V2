import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { MatchSingleComponent } from './match-single.component';

describe('MatchSingleComponent', () => {
  let component: MatchSingleComponent;
  let fixture: ComponentFixture<MatchSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ MatchSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(MatchSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
