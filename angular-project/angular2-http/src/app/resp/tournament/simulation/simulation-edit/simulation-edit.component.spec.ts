import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SimulationEditComponent } from './simulation-edit.component';

describe('SimulationEditComponent', () => {
  let component: SimulationEditComponent;
  let fixture: ComponentFixture<SimulationEditComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SimulationEditComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SimulationEditComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
