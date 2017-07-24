import { Injectable, OnInit } from '@angular/core';
import { Http, Response } from '@angular/http';
import { User } from "../models/user";

import { Observable } from "rxjs/Observable";
import 'rxjs/add/operator/catch';
import { Subject } from "rxjs/Subject";

@Injectable()
export class UserService {

    users: User[];
    private usersUrl: string = 'https://reqres.in/api/users';

    // observable src : contains data
    private userCreatedSource = new Subject<User>();
    private userDeletedSource = new Subject();

    // observable stream : made a subscription to this 
    userCreated$ = this.userCreatedSource.asObservable();
    userDeleted$ = this.userDeletedSource.asObservable();

    constructor(private http: Http){}

    /*
    * Get all users
    */
    getUsers(): Observable<User[]> {
        return this.http.get(this.usersUrl)
        .map(res => res.json().data)
        .map(users => users.map(this.toUser))
        .catch(this.handleError);
    }


    /*
    * Get a single user
    */
    getUser(id: number): Observable<User> {
        /*
        // attaching a token
        let headers = new Headers();
        let token   = localStorage.getItem('auth_token');
        headers.append('Content-Type', 'application/json');
        headers.append('Authorization', `Bearer ${token}`);
        */
        return this.http
            .get(`${this.usersUrl}/${id}`)
            .map(res => res.json().data)
            .map(this.toUser)
            .catch(this.handleError);
    }

    /*
    * Convert user info from API to our standard
    */
    private toUser(user): User {
        return {
                id: user.id,
                name: `${user.first_name} ${user.last_name}`,
                username: user.first_name,
                avatar: user.avatar,
            };
    }

    /**
     * Update the user
     */
    updateUser(user: User): Observable<User> {
        return this.http.put(`${this.usersUrl}/${user.id}`, user)
        .map(res => res.json())
        .catch(this.handleError);
    }

    /**
     * Delete the user
     */
    deleteUser(id: number) : Observable<any>{
        return this.http.delete(`${this.usersUrl}/${id}`)
        .do(res => this.userDeleted())
        .catch(this.handleError);
    }
    
    /**
     * Cretae the user
     */
    createUser(user: User): Observable<User> {
        return this.http.post(`${this.usersUrl}`, user)
        .map(res => res.json())
        .do(res => this.userCreated(res))
        .catch(this.handleError);
    }    

    /*
    /* The user was created. Add this info to our stream
     */
    private userCreated(user:User){
        this.userCreatedSource.next(user);
    }

    /*
    /* The user was deleted. Add this info to our stream
     */
    private userDeleted(){
        this.userDeletedSource.next();
    }

    /*
    /* Handle any errors from the api
     */
    private handleError(err){
        let errMessage: string;

        if (err instanceof Response){ 
            let body = err.json() || '';
            let error = body.error || JSON.stringify(body);
            errMessage = `${err.status} - ${err.statusText || ''} ${error}`;
        }
        else{
            errMessage = err.message ? err.message : err.toString();
        }

        return Observable.throw(errMessage);
        // return Observable.throw(err.json().data || 'Server error.');
    }



}