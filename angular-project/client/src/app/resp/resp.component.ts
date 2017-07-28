import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-resp',
  template: `
    <router-outlet></router-outlet>
  `,
  styleUrls: ['./resp.component.css']
})
export class RespComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
