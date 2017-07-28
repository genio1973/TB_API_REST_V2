import { Component, OnInit } from '@angular/core';
import { UserService } from "../../../shared/services/user.service";
import { User } from "../../../shared/models/user";

@Component({
  selector: 'my-user-list',
  templateUrl:'./user-list.component.html',
  styleUrls: ['./user-list.component.css']
})

export class UserListComponent implements OnInit {

    users: User[];  

    constructor( private userService: UserService ){}

    ngOnInit(): void {
      this.userService.getUsers()
          .subscribe(
            users => this.users = users);

           // console.log(`GROUPS OBJECT : ${this.users}`);
            /*
          .subscribe(
            users => {
              console.log(users);
              this.users = users
            });
            */
  }

}
