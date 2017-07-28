import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-teams',
  template: `
    <p>
      teams Works!
      <my-coachs></my-coachs>
    </p>
  `,
  styleUrls: ['./teams.component.css']
})
export class TeamsComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
