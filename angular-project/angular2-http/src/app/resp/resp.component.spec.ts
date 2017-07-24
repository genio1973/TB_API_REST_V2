import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { Resp1Component } from './resp.component';

describe('Resp1Component', () => {
  let component: RespComponent;
  let fixture: ComponentFixture<RespComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ Resp1Component ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(Resp1Component);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
