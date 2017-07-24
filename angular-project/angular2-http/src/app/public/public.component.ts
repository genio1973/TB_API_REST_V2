import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-public',
  template: `
    <h2>ZONE PUBLIC</h2>
    <router-outlet></router-outlet>
  `,
  styleUrls: ['./public.component.css']
})
export class PublicComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
