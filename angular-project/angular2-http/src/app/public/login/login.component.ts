import { Component, OnInit } from '@angular/core';
import { AuthService } from "../../shared/services/auth.service";
import { Router } from "@angular/router";

@Component({
  selector: 'app-login',
  templateUrl: `./login.component.html`,
  styleUrls: ['./login.component.css']
})

export class LoginComponent implements OnInit {

  credentials = { email:'plinio.sacchetti@bluewin.ch', password: 'pass'};
  successMessage: string = '';
  errorMessage: string = '';

  constructor( private authService: AuthService, private router: Router ) { }

  ngOnInit() {
  }

  

  login(){
    this.errorMessage = '';

    this.authService.login(this.credentials.email, this.credentials.password)
      .subscribe(
        data => {
          // Get the redirect URL from our auth service
          // If no redirect has been set, use the default
          let redirect = this.authService.redirectUrl ? this.authService.redirectUrl : '/admin';

          this.router.navigate([redirect]);
          console.log(data); 
        },
        err => {
          this.errorMessage = err;
          console.error(err);
        }
      ); 
  }

}
