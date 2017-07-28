import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'my-groups',
  templateUrl: './groups.component.html',
  styleUrls: ['./groups.component.css']
})

export class GroupsComponent implements OnInit {

  successMessage: string = '';
  errorMessage: string = '';

  constructor() { }

  ngOnInit() {

  }

}