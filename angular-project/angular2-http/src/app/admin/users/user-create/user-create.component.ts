import { Component, OnInit } from '@angular/core';
import { User } from "../../../shared/models/user";
import { UserService } from "../../../shared/services/user.service";
import { Router } from "@angular/router";

@Component({
  selector: 'my-user-create',
  templateUrl: './user-create.component.html',
  styleUrls: ['./user-create.component.css']
})
export class UserCreateComponent implements OnInit {

    user: User =  { name:'', username:'',  avatar:''};
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private userService: UserService,
                private router: Router) { }

    ngOnInit() {
      this.user = new User();
    }

    createUser(){
        this.errorMessage = '';
        this.successMessage = '';
        this.userService.createUser(this.user)
          .subscribe(
            user => {
              this.successMessage = 'User was created.';
              this.router.navigate(['/admin/users']);
              //console.log('user was created');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }

}
