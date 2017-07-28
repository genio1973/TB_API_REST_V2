import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-public',
  template: `
    <router-outlet></router-outlet>
  `,
  styleUrls: ['./public.component.css']
})
export class PublicComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
