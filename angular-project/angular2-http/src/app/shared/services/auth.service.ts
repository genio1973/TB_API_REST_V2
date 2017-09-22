import { Injectable } from '@angular/core';
import { Http, Response, RequestOptions, Headers } from "@angular/http";
import { Observable } from "rxjs/Observable";
import { Subject } from "rxjs/Subject";
import { ApiResponse } from "../models/api-response";
import { environment } from "../../../environments/environment";


@Injectable()
export class AuthService {
 
  //private authUrl: string = 'http://tournoi.romandvolley.ch/api/v1/public/user';
  private authUrl: string = environment.apiUrl + '/public/user';
  private loggedIn: boolean = false;
  private loggedInAsAdmin: boolean = false;
  private loggedUser = {email: '', droits:'', id_role:''};
  
  // store the URL so we can redirect after logging in
  redirectUrl: string;
  
  //observable src : contains data
  private authLoginSource = new Subject<any>();
  private authLogoutSource = new Subject();

  // observable stream : made a subscription to this 
  loginConnected$ = this.authLoginSource.asObservable();
  logout$ = this.authLogoutSource.asObservable();

  constructor(private http: Http) {
    // look at localStorage to check if the user is logged in
    this.loggedIn = !!localStorage.getItem('token');
  }

  /**
   * Check if the user is logged in
   */
  isLoggedIn() {
    return this.loggedIn;
  }

    /**
   * Check if the user is logged in as super-administrator
   */
  isLoggedInAsAdmin() {
    return this.loggedInAsAdmin;
  }

  /**
   * Log the user in
   */
  login(email: string, password: string): Observable<string> {
    return this.http.post(`${this.authUrl}/login`,{ 'email':email, 'password':password }, this.headBuilder)
      .do(this.checkError)
      .map(res => res.json().result)
      .do(res => {
        if (!res.error) {
          localStorage.setItem('token', res.token);
          localStorage.setItem('id_user', res.id_user);
          localStorage.setItem('email', res.email);
          localStorage.setItem('droits', res.droits);
          this.loggedUser.email = email;
          this.loggedUser.droits = res.droits;
          this.loggedUser.id_role = res.id_role;
          // this.authLoginSource.next(`${email} (${res.droits})`);
          this.authLoginSource.next(this.loggedUser);
          this.loggedIn = true;
          if(res.id_role == 1) { this.loggedInAsAdmin = true }
        }
      })
      .catch((e) => this.handleError(e));
  }

   /**
    * Header preparation, contentType
    **/
    private headBuilder(): RequestOptions{
        let headers = new Headers();
        headers.append('Content-Type', 'application/json');
        return new RequestOptions({ headers: headers }); // Create a request option
    }

  /**
   * Log the user out
   */
  logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('id_user');
    localStorage.removeItem('email');
    localStorage.removeItem('droits');
    this.authLogoutSource.next();
    this.loggedIn = false;    
    this.loggedInAsAdmin = false;
  }
  
    /*
    * Check if error comes from API
    */
    private checkError(res : Response) {
        let apiResp : ApiResponse = res.json();
        if(apiResp.error){
            localStorage.clear();
            throw new Error( apiResp.result || 'Server error.');
        }                
    } 

  /**
   * Handle any errors from the API
   */
  private handleError(err) {
    let errMessage: string;

    if (err instanceof Response) {
      let body   = err.json() || '';
      let error  = body.error || JSON.stringify(body);
      errMessage = `${err.status} - ${err.statusText || ''} ${error}`;
    } else {
      errMessage = err.message ? err.message : err.toString();
    }

    return Observable.throw(errMessage);
  }

}
