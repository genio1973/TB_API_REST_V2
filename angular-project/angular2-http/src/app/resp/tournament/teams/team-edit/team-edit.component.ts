import { Component, OnInit } from '@angular/core';
import { Team } from "../../../../shared/models/team";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { Router, ActivatedRoute } from "@angular/router";
import { Coach } from "../../../../shared/models/coach";
import { Group } from "../../../../shared/models/group";
import { Tournament } from "../../../../shared/models/tournament";

@Component({
  selector: 'my-team-edit',
  templateUrl: './team-edit.component.html',
  styleUrls: ['./team-edit.component.css']
})
export class TeamEditComponent implements OnInit {
    team: Team = { id_equipe: null, nom_equipe:'', niveau:'', nb_pts:0 ,id_groupe: 0};
    teamId: number;
    tournamentId: number;
    tournament: Tournament;
    coachs: Coach[];
    coach: Coach = {id_personne:0, nom:'', prenom: '', courriel:''};  
    
    group: Group;
    groups: Group[];
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private publicService: PublicTournamentService,
                private respService: RespTournamentService,
                private router: Router,
                private route: ActivatedRoute) {                   
                }

    ngOnInit() {
      
      let tournamentId = 0;
      
      // get the id from the url
      this.teamId = this.route.snapshot.params['idteam'];
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
        this.tournamentId = params['idtournoi'];
      });

      this.publicService.getTournament(this.tournamentId)
          .subscribe( t => this.tournament = t);

      this.publicService
          .getTeam(this.teamId)
          .subscribe(team => this.team = team);

      this.respService
        .getCoachs()
        .subscribe(coachs => {
          this.coachs = coachs;
          this.coach = coachs.find(c => c.id_personne == this.team.id_personne);
         } ); 

      this.publicService
      .getGroupsTournament(this.tournamentId)
      .subscribe(groups => {
        this.groups = groups;
        this.group = groups.find( g => g.id_groupe == this.team.id_groupe)
      }); 
    }


    /**
     * Delete the team
     */
        deleteTeam(){
        this.errorMessage = '';
        this.successMessage = '';
        this.respService.deleteTeam(this.team.id_equipe)
          .subscribe(
            data => {
              this.successMessage = 'Equipe supprimée.';
               this.router.navigate([`/responsible/tournament/${this.tournamentId}/groups/list`]);
            } ,
            err => {
              this.errorMessage = err;
            });
    }

    /**
     * Udpate team
     */
        updateTeam(){
        this.errorMessage = '';
        this.successMessage = '';
        console.log(this.team);

        this.respService.updateTeam(this.team)
          .subscribe(
            user => {
              this.successMessage = 'Equipe a été mise à jour.';
              this.router.navigate([`/responsible/tournament/${this.tournamentId}/teams/${this.team.id_equipe}`]);
            },
            err => {
              this.errorMessage = err;
            });
    }

}