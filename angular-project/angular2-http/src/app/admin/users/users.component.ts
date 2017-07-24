import { Component, OnInit } from '@angular/core';
import { UserService } from "../../shared/services/user.service";
import { User } from "../../shared/models/user";

@Component({
  selector: 'my-users',
  templateUrl: './users.component.html',
  styleUrls: ['./users.component.css']
})

export class UsersComponent implements OnInit {
  successMessage: string = '';
  errorMessage: string = '';
  private user: User;

  constructor(private service: UserService) { }

  ngOnInit() {
      // user created
      this.service.userCreated$.subscribe(user => {
      this.successMessage = `${user.name} has been created !`;
      this.clearMessages();
    });

      // user deleted
      this.service.userDeleted$.subscribe(data =>{
      this.successMessage = `The user has been deleted !`;
      this.clearMessages();
    });
  }

  /**
   * Clear all messages after 5 sec
   */
  
  clearMessages(){
    setTimeout(() => {
      this.errorMessage = '';
      this.successMessage = '';  
    }, 5000);

  }

}
