import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { CoachEditComponent } from './coach-edit.component';

describe('CoachEditComponent', () => {
  let component: CoachEditComponent;
  let fixture: ComponentFixture<CoachEditComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ CoachEditComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(CoachEditComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
