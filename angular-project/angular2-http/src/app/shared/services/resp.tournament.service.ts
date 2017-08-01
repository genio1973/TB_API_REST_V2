import { Injectable, OnInit } from '@angular/core';
import { Http, Response, RequestOptions, Headers } from '@angular/http';
import { Tournament } from "../models/tournament";

import { Observable } from "rxjs/Observable";
import 'rxjs/add/operator/catch';

import { ApiResponse } from "../models/api-response";
import { Subject } from "rxjs/Subject";
import { TournamentSimple } from "../models/tournament-simple";
import { Group } from "../models/group";
import { Team } from "../models/team";
import { Coach } from "../models/coach";

@Injectable()
export class RespTournamentService {

    tournaments: Tournament[];
    private tournamentUrl: string = 'http://test.romandvolley.ch/api/v3/resp';

    // observable src : contains data
    private tournamentSource = new Subject<string>();

    // observable stream : made a subscription to this 
    tournament$ = this.tournamentSource.asObservable();


    constructor(private http: Http){}

    /*
    * Header preparation, contentType
    * APIKEY and userid
    */
    private headBuilder(): RequestOptions{
        let headers = new Headers();
        let token   = localStorage.getItem('token');
        let userid   = localStorage.getItem('id_user');
        
        headers.append('Content-Type', 'application/json');
        headers.append('userid', `${userid}`);
        headers.append('APIKEY', `${token}`);
        return new RequestOptions({ headers: headers }); // Create a request option
    }


    /**
     * Get coaches managed ba this responsible
     */
    getCoachs(): Observable<Coach[]>{

         return this.http                    
            .get(`${this.tournamentUrl}/personnes`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            .catch((e) => this.handleError(e));
    }

    /**
     * Get teams'coach
     * @param coach id du coach
     */
    getTeamsByCoach(id: number): Observable<Team[]>{

         return this.http                    
            .get(`${this.tournamentUrl}/personne/${id}/equipes`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            .catch((e) => this.handleError(e));
    }

    /**
     * Get teams from a tournament with coach details
     * @param id_tournament identifiant du groupe 
     */
    getTournamentTeams(id_tournament: number): Observable<Team[]>{
         return this.http                    
            .get(`${this.tournamentUrl}/tournament/${id_tournament}/equipes`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            .catch((e) => this.handleError(e));
    }

    /*
    * Get all Tournament by statut
    */
    getTournamentsByStatut(id: number): Observable<Tournament[]> {

         return this.http                    
                    .get(`${this.tournamentUrl}/tournaments/statut/${id}`, this.headBuilder())
                    .do(this.checkError)
                    .map(res => res.json().result)
                    .map(tournaments => tournaments.map(this.toTournament))
                    .catch((e) => this.handleError(e));
    }

    /**
     * Get all statuts
     */
    getAllTounamentStatuts(){
         return this.http                    
            .get(`${this.tournamentUrl}/tournaments/statuts`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            .catch((e) => this.handleError(e));
    }


    /*
    * Get all Tournament by statut
    */
    getTournaments(): Observable<Tournament[]> {

         return this.http
                    .get(`${this.tournamentUrl}/tournaments`, this.headBuilder())
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
            .get(`${this.tournamentUrl}/tournament/${id}`, this.headBuilder())
            .do(this.checkError)
            .map(res => res.json().result)
            .map(this.toTournament)
            .catch((e) => this.handleError(e));
    }

    /**
     * Create a new tournament
     * @param tournament 
     */
    createTournament(tournament: Tournament): Observable<Tournament> {
        return this.http.post(`${this.tournamentUrl}/tournoi`, tournament, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentCreated(res))
        .catch((e) => this.handleError(e));
    }


    /**
     * The tournament was created. Add this info to our stream
     * @param tournament 
     */
    private tournamentCreated(tournament:Tournament){
        this.tournamentSource.next("Tournoi a été créé.");
    }

    /**
     * Update tournament
     * @param tournament 
     */
        updateTournament(tournament: Tournament): Observable<Tournament> {
        // attaching a token
        return this.http.put(`${this.tournamentUrl}/tournoi/${tournament.id}`, tournament, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentUpdated())
        .catch((e) => this.handleError(e));
    }

    
    /**
     * Update coach
     * @param coach 
     */
        updateCoach(coach: Coach): Observable<Coach> {
            console.log(coach);
        // attaching a token
        return this.http.put(`${this.tournamentUrl}/personne/${coach.id_personne}`, coach, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentUpdated())
        .catch((e) => this.handleError(e));
    }

    /**
    /* The tournament was updated. Add this info to our stream
    **/
    private tournamentUpdated(){        
        this.tournamentSource.next("Le tournoi a été mis à jour.");      
    }


    /**
     * Delete a tournament
     * @param id 
     */
    deleteTournament(id: number) : Observable<any>{
        return this.http.delete(`${this.tournamentUrl}/tournoi/${id}`, this.headBuilder())
        .do(this.checkError)
        .do(res => this.tournamentDeleted())
        .catch((e) => this.handleError(e));
    }


    /**
     * Delete a team
     * @param id 
     */
    deleteTeam(id: number) : Observable<any>{
        return this.http.delete(`${this.tournamentUrl}/equipe/${id}`, this.headBuilder())
        .do(this.checkError)
        .catch((e) => this.handleError(e));
    }

    /**
     * Delete a coach
     * @param id 
     */
    deleteCoach(id: number) : Observable<any>{
        return this.http.delete(`${this.tournamentUrl}/personne/${id}`, this.headBuilder())
        .do(this.checkError)
        .catch((e) => this.handleError(e));
    }


    /*
    /* The tournament was deleted. Add this info to our stream
     */
    private tournamentDeleted(){
        this.tournamentSource.next("Le tournoi a été supprimé.");
    }
    /**
    * Convert tournament info from API to our standard
    * @param tournament 
    */
    private toTournament(tournament): Tournament {
        return {
                id: tournament.id_tournoi,
                date_debut: tournament.date_debut,
                nom_tournoi: tournament.nom_tournoi,               
                id_user: tournament.id_user,
                id_statut: tournament.id_statut_tournoi,
                statut_tournoi: tournament.statut_tournoi,
                email: tournament.email,
                nom_user: tournament.nom_user,
                prenom_user: tournament.prenom_user,
            };
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

    
    /**
     * Création de groupes dans un tournoi
     * @param groups
     */
    createGroups(groups: Group[]): Observable<Group> {
        return this.http.post(`${this.tournamentUrl}/groupes`, groups, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentCreated(res))
        .catch((e) => this.handleError(e));
    }

     /**
     * Création d''équipes
     * @param teams : tableau d'équipes
     */
    createTeams(teams: Team[]): Observable<Team> {
        return this.http.post(`${this.tournamentUrl}/equipes`, teams, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentCreated(res))
        .catch((e) => this.handleError(e));
    }   

    
    /**
     * Création de personnes (coach)
     * @param coachs : tableau des personnes
     */
    createCoachs(coachs: Coach[]): Observable<Coach> {
        return this.http.post(`${this.tournamentUrl}/personnes`, coachs, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentCreated(res))
        .catch((e) => this.handleError(e));
    }  

    /**
     * Mise à jour du groupe
     * @param group 
     */
        updateGroup(group: Group): Observable<Tournament> {
        // attaching a token
        return this.http.put(`${this.tournamentUrl}/groupe/${group.id_groupe}`, group, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentUpdated())
        .catch((e) => this.handleError(e));
    }

    /**
     * Mise à jour d'une équipe
     * @param team 
     */
        updateTeam(team: Team): Observable<Tournament> {
        // attaching a token
        return this.http.put(`${this.tournamentUrl}/equipe/${team.id_equipe}`, team, this.headBuilder())
        .do(this.checkError)
        .map(res => res.json())
        .do(res => this.tournamentUpdated())
        .catch((e) => this.handleError(e));
    }

}