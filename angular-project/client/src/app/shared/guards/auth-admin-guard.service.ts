import { Injectable } from '@angular/core';
import { CanActivate, Router, RouterStateSnapshot, ActivatedRouteSnapshot, CanActivateChild } from '@angular/router';
import { Observable } from "rxjs/Observable";
import { AuthService } from "../services/auth.service";

@Injectable()
export class AuthAdminGuardService implements CanActivate, CanActivateChild{

  constructor(private authService: AuthService, private router: Router) { }

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    //console.log(`AuthGuard#canActivate called : idLogged = ${this.authService.isLoggedIn()}`);
    
    let url: string = state.url;
    return this.checkLogin(url);
  }

  canActivateChild(childRoute: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean | Observable<boolean> | Promise<boolean> {
    //console.log('AuthGuardChild#canActivate called');
    return this.canActivate(childRoute, state);
  }

  checkLogin(url: string): boolean {
    if (this.authService.isLoggedInAsAdmin()) { return true; }

    // Store the attempted URL for redirecting
    this.authService.redirectUrl = url;

    // Navigate to the login page with extras
    this.router.navigate(['/public/login']);
    return false;
  }

}
