import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ResponsibleCreateComponent } from './responsible-create.component';

describe('ResponsibleCreateComponent', () => {
  let component: ResponsibleCreateComponent;
  let fixture: ComponentFixture<ResponsibleCreateComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ResponsibleCreateComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ResponsibleCreateComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
