import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ResultSingleComponent } from './result-single.component';

describe('ResultSingleComponent', () => {
  let component: ResultSingleComponent;
  let fixture: ComponentFixture<ResultSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ResultSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ResultSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
