import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CoachSingleComponent } from './coach-single.component';

describe('CoachSingleComponent', () => {
  let component: CoachSingleComponent;
  let fixture: ComponentFixture<CoachSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CoachSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CoachSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
