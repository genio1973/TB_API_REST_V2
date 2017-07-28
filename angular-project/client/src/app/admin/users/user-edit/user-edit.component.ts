import { Component, OnInit } from '@angular/core';
import { User } from "../../../shared/models/user";
import { UserService } from "../../../shared/services/user.service";
import { ActivatedRoute, Router } from "@angular/router";

@Component({
  selector: 'my-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css']
})
export class UserEditComponent implements OnInit {
    user:User;
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private userService: UserService,
                private route: ActivatedRoute,
                private router: Router) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.userService
          .getUser(id)
          .subscribe(user=> this.user = user);
    }

    updateUser(){
        this.errorMessage = '';
        this.successMessage = '';
        this.userService.updateUser(this.user)
          .subscribe(
            user => {
              this.successMessage = 'User was updated.';
              this.router.navigate(['/admin/users']);
              //console.log('user was updated');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }


}
