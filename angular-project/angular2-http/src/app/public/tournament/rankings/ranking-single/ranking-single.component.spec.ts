import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { RankingSingleComponent } from './ranking-single.component';

describe('RankingSingleComponent', () => {
  let component: RankingSingleComponent;
  let fixture: ComponentFixture<RankingSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ RankingSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(RankingSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
