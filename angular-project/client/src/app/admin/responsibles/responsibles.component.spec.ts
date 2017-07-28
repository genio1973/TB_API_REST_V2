import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ResponsiblesComponent } from './responsibles.component';

describe('ResponsiblesComponent', () => {
  let component: ResponsiblesComponent;
  let fixture: ComponentFixture<ResponsiblesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ResponsiblesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ResponsiblesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
