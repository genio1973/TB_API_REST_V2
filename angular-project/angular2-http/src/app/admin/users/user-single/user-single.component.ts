import { Component, OnInit } from '@angular/core';
import { User } from "../../../shared/models/user";
import { UserService } from "../../../shared/services/user.service";
import { Router, ActivatedRoute } from '@angular/router';

@Component({
  selector: 'my-user-single',
  templateUrl: './user-single.component.html',
  styleUrls: ['./user-single.component.css']
})
export class UserSingleComponent implements OnInit {

    user: User;
    errorMessage = '';
    successMessage = '';

    constructor(private userService:UserService,
                private router: Router,
                private route: ActivatedRoute) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.userService
        .getUser(id)
        .subscribe(user => this.user = user);
    }

    deleteUser(){
        this.errorMessage = '';
        this.successMessage = '';
        this.userService.deleteUser(this.user.id)
          .subscribe(
            data => {
              this.successMessage = 'User was deleted.';
              this.router.navigate(['/admin/users']);
              //console.log('user was deleted');
            } ,
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }
}
