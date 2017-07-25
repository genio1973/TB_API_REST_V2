import { Injectable, OnInit } from '@angular/core';
import { Http, Response } from '@angular/http';
import { Tournament } from "../models/tournament";

import { Observable } from "rxjs/Observable";
import 'rxjs/add/operator/catch';

import { ApiResponse } from "../models/api-response";
import { Subject } from "rxjs/Subject";

@Injectable()
export class TournamentService {

    tournaments: Tournament[];
    private tournamentUrl: string = 'http://test.romandvolley.ch/api/v3/public';

    // observable src : contains data
    private tournamentSource = new Subject<string>();

    // observable stream : made a subscription to this 
    tournament$ = this.tournamentSource.asObservable();


    constructor(private http: Http){}

    /*
    * Get all Tournament by statut
    */
    getTournamentsByStatut(id: number): Observable<Tournament[]> {

         return this.http                    
                    .get(`${this.tournamentUrl}/tournaments/statut/${id}`)
                    .do(this.checkError)
                    .map(res => res.json().result)
                    .map(tournaments => tournaments.map(this.toTournament))
                    .catch((e) => this.handleError(e));
    }

    /*
    * Get all Tournament by statut
    */
    getTournaments(): Observable<Tournament[]> {

         return this.http
                    .get(`${this.tournamentUrl}/tournaments`)
                    .do(this.checkError)
                    .map(res => res.json().result)
                    .map(tournaments => tournaments.map(this.toTournament))
                    .catch((e) => this.handleError(e));
    }

    /*
    * Get a single user
    */
    getTournament(id: number): Observable<Tournament> {
        return this.http
            .get(`${this.tournamentUrl}/tournament/${id}`)
            .do(this.checkError)
            .map(res => res.json().result)
            .map(this.toTournament)
            .catch((e) => this.handleError(e));
    }

    /*
    * Convert user info from API to our standard
    */
    private toTournament(tournament): Tournament {
        return {
                id: tournament.id_tournoi,
                name: tournament.nom_tournoi,
                id_statut: tournament.id_statut,
                id_user: tournament.id_user,
            };
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
        this.tournamentSource.next(errMessage);
        return Observable.throw(errMessage);
        // return Observable.throw(err.json().data || 'Server error.');
    }

}