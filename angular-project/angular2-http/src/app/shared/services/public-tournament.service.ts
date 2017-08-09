import { Injectable, OnInit } from '@angular/core';
import { Http, Response, RequestOptions, Headers } from '@angular/http';
import { Tournament } from "../models/tournament";

import { Observable } from "rxjs/Observable";
import 'rxjs/add/operator/catch';

import { ApiResponse } from "../models/api-response";
import { Subject } from "rxjs/Subject";
import { Group } from "../models/group";
import { Team } from "../models/team";
import { Pitch } from "../models/pitch";
import { Resultat } from "../models/resultat";
import { ScoreSet } from "../models/score-set";
import { Ranking } from "../models/ranking";

@Injectable()
export class PublicTournamentService {

    tournaments: Tournament[];
    private tournamentUrl: string = 'http://test.romandvolley.ch/api/v3/public';

    // observable src : contains data
    private tournamentSource = new Subject<string>();

    // observable stream : made a subscription to this 
    tournament$ = this.tournamentSource.asObservable();


    constructor(private http: Http){}

   /** 
    * Header preparation, contentType
    */
    private headBuilder(): RequestOptions{
        let headers = new Headers();        
        headers.append('Content-Type', 'application/json');
        return new RequestOptions({ headers: headers }); // Create a request option
    }

    /*
    * Get all Tournament by statut
    */
    getTournamentsByStatut(id: number): Observable<Tournament[]> {

         return this.http                    
                    .get(`${this.tournamentUrl}/tournois/statut/${id}`)
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
                    .get(`${this.tournamentUrl}/tournois`)
                    .do(this.checkError)
                    .map(res => res.json().result)
                    .map(tournaments => tournaments.map(this.toTournament))
                    .catch((e) => this.handleError(e));
    }

    /*
    * Get a single tournament
    */
    getTournament(id: number): Observable<Tournament> {
        return this.http
            .get(`${this.tournamentUrl}/tournoi/${id}`)
            .do(this.checkError)
            .map(res => res.json().result)
            .map(this.toTournament)
            .catch((e) => this.handleError(e));
    }

    /*
    * Convert tournament info from API to our standard
    */
    private toTournament(tournament): Tournament {
        return {
                id: tournament.id_tournoi,
                date_debut: tournament.date_debut,
                nom_tournoi: tournament.nom_tournoi,

                id_user: tournament.id_user,
                id_statut: tournament.id_statut,
                statut_tournoi: tournament.statut_tournoi,
            };
    }


    /**
     * Get groups from a tournament
     * @param id identifiant du tournoi 
     */
    getGroupsTournament(id: number): Observable<Group[]>{
         return this.http                    
            .get(`${this.tournamentUrl}/tournoi/${id}/groupes`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)            
            .catch((e) => this.handleError(e));
    }
        

    /**
     * Get pitches from a tournament
     * @param id identifiant du tournoi 
     */
    getTournamentPitches(id: number): Observable<Pitch[]>{
         return this.http                    
            .get(`${this.tournamentUrl}/tournoi/${id}/terrains`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)            
            .catch((e) => this.handleError(e));
    }


    /**
     * Get teams from a tournament
     * @param id_tournament identifiant du groupe 
     */
    getTournamentTeams(id_tournament: number): Observable<Team[]>{
         return this.http                    
            .get(`${this.tournamentUrl}/tournoi/${id_tournament}/equipes`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            .catch((e) => this.handleError(e));
    }


    /**
     * Get teams from a group
     * @param id_group identifiant du groupe 
     */
    getTeamsGroup(id_group: number): Observable<Team[]>{
         return this.http                    
            .get(`${this.tournamentUrl}/equipes/groupe/${id_group}`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            //.map(this.toTeam)
            //.find( t => t.id_equipe == id_group)
            .catch((e) => this.handleError(e));
    }


    /**
     * Get groups and their teams from a tournament
     * @param id_tournament identifiant du tournoi 
     */
    getGroupsAndTeams(id_tournament: number): Observable<Group[]>{
         return this.http                    
            .get(`${this.tournamentUrl}/tournoi/${id_tournament}/groupes/equipes`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            .map(groups => groups.map(this.toGoupTeams))
            .catch((e) => this.handleError(e));
    }

    /*
    * Convert tournament info from API to our standard
    */
    private toGoupTeams(group): Group {
        //console.log(group);
        return {
                id_groupe: group.id_groupe,
                nom_groupe: group.nom_groupe,
                id_tournoi: group.id_tournoi,
                teams: group.equipes
            };
    }


    /*
    * Convert tournament info from API to our standard
    */
    // private toTeam(team): Team {
    //     return {
    //             id_equipe: team.id_equipe,
    //             niveau: team.niveau,
    //             nom_equipe: team.nom_equipe,
    //             nb_pts: team.nb_pts,
    //             id_groupe: team.id_groupe,
    //         };
    // }



    /**
     * Get team from a group
     * @param id identifiant du groupe 
     */
    getTeam(id: number): Observable<Team>{
         return this.http                    
            .get(`${this.tournamentUrl}/equipe/${id}`, this.headBuilder())
            .do(this.checkError)
            .map(res => {
                    let teams = res.json().result;
                    return teams.find(t => t.id_equipe === id);
            })
            .catch((e) => this.handleError(e));
    }


    /*
    * Get all matchs results in a tournament
    */
    getResultsByTournament(id: number): Observable<Resultat[]> {
         return this.http                    
                    .get(`${this.tournamentUrl}/tournoi/${id}/resultats`)
                    .do(this.checkError)
                    .map(res =>res.json().result)
                    .catch((e) => this.handleError(e));
    }

    /*
    * Get a match with result
    */
    getMatchResult(id: number): Observable<Resultat> {
         return this.http                    
                    .get(`${this.tournamentUrl}/match/${id}/resultat`)
                    .do(this.checkError)
                    .map(res =>res.json().result)
                    .catch((e) => this.handleError(e));
    }


    /*
    * Get all Tournament all groups ranking in a tournament
    */
    getRankingsByTournament(id: number): Observable<Ranking[]> {
         return this.http                    
                    .get(`${this.tournamentUrl}/classement/tournoi/${id}`)
                    .do(this.checkError)
                    .map(res => res.json().result)
                    .catch((e) => this.handleError(e));
    }



    /*
    * Check if error comes from API
    */
    private checkError(res : Response) {
        let apiResp : ApiResponse = res.json();
        if(apiResp.error){
            //localStorage.clear();
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