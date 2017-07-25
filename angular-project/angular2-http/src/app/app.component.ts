import { Component } from '@angular/core';
import { AuthService } from "./shared/services/auth.service";
import { Router } from "@angular/router";

@Component({
  selector: 'my-app',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})

export class AppComponent  {

  userConnectedEmail: string ='';
  constructor(private authService: AuthService,
              private router: Router){
  }

  ngOnInit(): void {
 
      this.authService.loginConnected$.subscribe( email => {            
            this.userConnectedEmail = email;
          }); 

      this.authService.logout$.subscribe( data => {            
            this.userConnectedEmail = '';
          }); 
  }
    /**
   * Is the user logged in?
   */
  get isLoggedIn() {
    return this.authService.isLoggedIn();
  }

  /**
   * log ther user out
   */
  logout(){
      console.log("logoutttttt");
      this.authService.logout();
      this.router.navigate(['/public/login']);
  }
}
