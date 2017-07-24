import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-admin',
  template: `
    <h2>ZONE D'ADMINISTRATION</h2>
    <router-outlet></router-outlet>
  `,
  styleUrls: ['./admin.component.css']
})

export class AdminComponent {

}
