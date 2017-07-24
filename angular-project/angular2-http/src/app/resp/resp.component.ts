import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-resp',
  template: `
      <h2>ZONE RESPONSABLES TOURNOIS</h2>
    <router-outlet></router-outlet>
  `,
  styleUrls: ['./resp.component.css']
})
export class RespComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
