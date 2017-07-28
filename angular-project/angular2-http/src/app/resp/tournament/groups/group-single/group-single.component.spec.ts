import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { GroupSingleComponent } from './group-single.component';

describe('GroupSingleComponent', () => {
  let component: GroupSingleComponent;
  let fixture: ComponentFixture<GroupSingleComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ GroupSingleComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(GroupSingleComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
