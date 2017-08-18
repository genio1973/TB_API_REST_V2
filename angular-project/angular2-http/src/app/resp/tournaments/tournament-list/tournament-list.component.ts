import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../../shared/models/tournament";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { AuthService } from "../../../shared/services/auth.service";
import { Router } from "@angular/router";
import { ApiResponse } from "../../../shared/models/api-response";
import {  PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { Group } from "../../../shared/models/group";
import { Team } from "../../../shared/models/team";
import { Observable } from "rxjs/Observable";

@Component({
  selector: 'my-tournament-list',
  templateUrl: './tournament-list.component.html',
  styleUrls: ['./tournament-list.component.css']
})
export class TournamentListComponent implements OnInit {
    tournaments: Tournament[];

    constructor( private authService: AuthService,
                 private publicService: PublicTournamentService,
                 private respService: RespTournamentService,
                 private router: Router ){}

    ngOnInit(): void {
      this.respService.getTournaments()
          .subscribe(
            tournaments => this.tournaments = tournaments);
    }

 /**
 * Is the user logged in as administrator ?
 */
  get isLoggedInAsAdmin() {
    return this.authService.isLoggedInAsAdmin();
  }

  errorMessage: string = '';

  /**
   * Clone a tournament witout planning and reslut. Only groups and teams
   * @param tournamentId 
   */
  cloneTournament(tournamentId: number){
    
    // get tournament to clone and add timestamp to the name
    let tournament: Tournament = this.tournaments.find( t => t.id == tournamentId);
    tournament.nom_tournoi = new Date().getTime() + '_clone_' + tournament.nom_tournoi ;
    tournament.id_statut = 0;

    let groups: Group[] = [];
    let insertedId: number;
    let teams: Team[] = [];

    // insert tournament and get the new tournament id
    this.respService.createTournament(tournament)
        .map( res => { return res; })
        .subscribe(res => {
          insertedId = res.result.id_dernier_insert;

          //get all groups
          this.publicService.getGroupsAndTeams(tournamentId)
            .map(res => {return res;})
            .subscribe(gps => {
              groups = gps;

              //change id_tournoi
              groups.map(g => g.id_tournoi = insertedId);

              //insert the new groups
              this.respService.createGroups(groups)
              .map(res => { return res; })
              .subscribe( res => {
                  let firstInsertedId = res.result.id_premier_insert;

                  // foreach group, change id_group for each team
                  // and put all teams in a unique array to create them
                  let teams : Team[] = [];
                  groups.map( g => {
                    g.teams.map(t => {
                      t.id_groupe = firstInsertedId;
                      teams.push(t);
                    })
                    firstInsertedId++; // next id_groupe
                  })
                  
                  // insert teams in DB
                  this.respService.createTeams(teams)
                      .map(res => {return res;})
                      .subscribe(res => {
                        
                      });
                  
              })
            })
            this.router.navigate([`/responsible/tournaments/${insertedId}/edit`]);   
        })
  }

}



