import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ResponsibleSingleComponent } from './responsible-single.component';

describe('ResponsibleSingleComponent', () => {
  let component: ResponsibleSingleComponent;
  let fixture: ComponentFixture<ResponsibleSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ResponsibleSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ResponsibleSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
