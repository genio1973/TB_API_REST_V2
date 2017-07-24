import { Injectable, OnInit } from '@angular/core';
import { Http, Response } from '@angular/http';
import { Tournament } from "../models/tournament";

import { Observable } from "rxjs/Observable";
import 'rxjs/add/operator/catch';

@Injectable()
export class TournamentService {

    tournaments: Tournament[];
    private tournamentUrl: string = 'http://test.romandvolley.ch/api/v3/public';
    //private tournamentUrl: string = 'http://localhost/tournoibachelor/api/v3/public';

    constructor(private http: Http){}

    /*
    * Get all Tournament by statut
    */
    getTournamentsByStatut(id: number): Observable<Tournament[]> {

         return this.http
                    .get(`${this.tournamentUrl}/tournaments/statut/${id}`)
                    .map(res => res.json().result)
                    .map(tournaments => tournaments.map(this.toTournament))
                    .catch(this.handleError);
    }

    /*
    * Get all Tournament by statut
    */
    getTournaments(): Observable<Tournament[]> {

         return this.http
                    .get(`${this.tournamentUrl}/tournaments`)
                    .map(res => res.json().result)
                    .map(tournaments => tournaments.map(this.toTournament))
                    .catch(this.handleError);
    }

    /*
    * Get a single user
    */
    getTournament(id: number): Observable<Tournament> {
        /*
        // attaching a token
        let headers = new Headers();
        let token   = localStorage.getItem('auth_token');
        headers.append('Content-Type', 'application/json');
        headers.append('Authorization', `Bearer ${token}`);
        */
        return this.http
            .get(`${this.tournamentUrl}/tournament/${id}`)
            .map(res => res.json().result)
            .map(this.toTournament)
            .catch(this.handleError);
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