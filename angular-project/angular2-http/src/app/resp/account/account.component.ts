import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-account',
  template: `
    <p>
      account Works!
    </p>
  `,
  styleUrls: ['./account.component.css']
})
export class AccountComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
