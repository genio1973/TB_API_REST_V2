import { Injectable, OnInit } from '@angular/core';
import { Http, Response, RequestOptions, Headers } from '@angular/http';
import { Responsible } from "../models/responsible";

import { Observable } from "rxjs/Observable";
import { Subject } from "rxjs/Subject";

@Injectable()
export class ResponsibleService {

    responsibles: Responsible[];    
    private responsiblesUrl: string = 'http://test.romandvolley.ch/api/v3/admin';

    // observable src : contains data
    private responsibleCreatedSource = new Subject<Responsible>();
    private responsibleDeletedSource = new Subject();
    private responsibleUpdatedSource = new Subject();

    // observable stream : made a subscription to this 
    responsibleCreated$ = this.responsibleCreatedSource.asObservable();
    responsibleDeleted$ = this.responsibleDeletedSource.asObservable();
    responsibleUpdated$ = this.responsibleUpdatedSource.asObservable();

    constructor(private http: Http){}

    /*
    * Get all responsibles
    */
    getResponsibles(): Observable<Responsible[]> {       
        return this.http.get(`${this.responsiblesUrl}/users`, this.headBuilder())
            .map(res => res.json().result)
            .map(responsibles => responsibles.map(this.toResponsible))
            .catch(this.handleError);
    }

    /*
    * Header preparation, contentType
    * APIKEY and userid
    */
    private headBuilder(): RequestOptions{
        let headers = new Headers();
        let token   = localStorage.getItem('auth_token');
        
        token = '4e540c810c2d29228ac179b98be3dc35';

        headers.append('Content-Type', 'application/json');
        headers.append('userid', '1');
        headers.append('APIKEY', ` ${token}`);
        return new RequestOptions({ headers: headers }); // Create a request option
    }

    /*
    * Get a single responsible
    */
    getResponsible(id: number): Observable<Responsible> {
        return this.http
            .get(`${this.responsiblesUrl}/user/${id}`, this.headBuilder())
            .map(res => res.json().result)
            .map(this.toResponsible)
            .catch(this.handleError);
    }

    /*
    * Convert responsible info from API to our standard
    */
    private toResponsible(responsible): Responsible {
        return {
                id: responsible.id_user,
                email: responsible.email,
                token_expire: responsible.token_expire,
                nom_user: responsible.nom_user,
                prenom_user:  responsible.prenom_user,
                status:  responsible.status,
                id_role:  responsible.id_role,
                droits:  responsible.droits,
            };
    }

    /**
     * Update the responsible
     */
    updateResponsible(responsible: Responsible): Observable<Responsible> {
        // attaching a token
        return this.http.put(`${this.responsiblesUrl}/user/${responsible.id}`, responsible, this.headBuilder())
        .map(res => res.json())
        .do(res => this.responsibleUpdated())
        .catch(this.handleError);
    }

    /**
     * Delete the responsible
     */
    deleteResponsible(id: number) : Observable<any>{
        return this.http.delete(`${this.responsiblesUrl}/user/${id}`, this.headBuilder())
        .do(res => this.responsibleDeleted())
        .catch(this.handleError);
    }
    
    /**
     * Create the responsible
     */
    createResponsible(responsible: Responsible): Observable<Responsible> {
        return this.http.post(`${this.responsiblesUrl}`, responsible, this.headBuilder())
        .map(res => res.json())
        .do(res => this.responsibleCreated(res))
        .catch(this.handleError);
    }    
  
    /*
    /* The responsible was created. Add this info to our stream
     */
    private responsibleCreated(responsible:Responsible){
        this.responsibleCreatedSource.next(responsible);
        
    }

    /**
    /* The responsible was updated. Add this info to our stream
    **/
    private responsibleUpdated(){        
        this.responsibleUpdatedSource.next();      
    }

    /*
    /* The responsible was deleted. Add this info to our stream
     */
    private responsibleDeleted(){
        this.responsibleDeletedSource.next();
    }


    // **
    // * Handle any errors from the api
    // **
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