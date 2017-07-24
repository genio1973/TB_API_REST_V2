import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ResponsibleEditComponent } from './responsible-edit.component';

describe('ResponsibleEditComponent', () => {
  let component: ResponsibleEditComponent;
  let fixture: ComponentFixture<ResponsibleEditComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ResponsibleEditComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ResponsibleEditComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
