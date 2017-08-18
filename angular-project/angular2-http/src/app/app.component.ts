import { Component, enableProdMode } from '@angular/core';
import { AuthService } from "./shared/services/auth.service";
import { Router } from "@angular/router";
import { RolesEnum } from "./shared/models/rolesEnum";
enableProdMode();

@Component({
  selector: 'my-app',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})
 

export class AppComponent  {

  userConnected = {email: '', droits:'', id_role:RolesEnum.NONE};
  myRoleEnum = RolesEnum; 
  
  constructor(private authService: AuthService,
              private router: Router){
  }

  ngOnInit(): void {
 
      this.authService.loginConnected$.subscribe( user => {            
            this.userConnected.email = user.email;
            this.userConnected.droits = user.droits;
            this.userConnected.id_role = user.id_role;
          }); 

      this.authService.logout$.subscribe( data => {            
            this.userConnected.droits = '';
            this.userConnected.email = '';
            this.userConnected.id_role = RolesEnum.NONE;
          }); 
  }
  /**
   * Is the user logged in?
   */
  get isLoggedIn() {
    return this.authService.isLoggedIn();
  }

 /**
 * Is the user logged in as administrator ?
 */
  get isLoggedInAsAdmin() {
    return this.authService.isLoggedInAsAdmin();
  }
  /**
   * log ther user out
   */
  logout(){
      //console.log("logoutttttt");
      this.authService.logout();
      this.router.navigate(['/public/login']);
  }
}
