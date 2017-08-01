import { Component, OnInit } from '@angular/core';
import { Team } from "../../../../shared/models/team";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { ActivatedRoute, Router } from "@angular/router";
import { Group } from "../../../../shared/models/group";

@Component({
  selector: 'my-team-create',
  templateUrl: './team-create.component.html',
  styleUrls: ['./team-create.component.css']
})
export class TeamCreateComponent implements OnInit {
    //team: Team = { id_equipe: null, nom_equipe:'', niveau:'', nb_pts:0 ,id_groupe: 0};
    teams: Team[] =  [{ id_equipe: null, nom_equipe:'', niveau:'', nb_pts:0 ,id_groupe: 0 }];
    group: Group = { id_groupe: null, nom_groupe:'', id_tournoi:null};
    groups: Group[];
    
    tournamentId: number;
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private publicService: PublicTournamentService,
                private respService: RespTournamentService,
                private router: Router,
                private route: ActivatedRoute) {                   
                }

    ngOnInit() {
      
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
          this.tournamentId = params['idtournoi'];
        });

        // Est-ce que l'on a un goupe dans la route ? alors l'équipe sera dans ce groupe !
        if(this.route.snapshot.params['idgroup']) {
          this.teams[0].id_groupe = this.route.snapshot.params['idgroup'];
          this.publicService
              .getGroupsTournament(this.tournamentId)
              .subscribe(groups => { 
                this.group = groups.find(g => g.id_groupe == this.teams[0].id_groupe)
              });
        }
        else {
          this.publicService
              .getGroupsTournament(this.tournamentId)
              .subscribe(groups => this.groups = groups);
      }
    }

    createTeam(){
        this.errorMessage = '';
        this.successMessage = '';

        this.respService.createTeams(this.teams)
          .subscribe(
            data => {
              this.successMessage = `L'équipe a été créé.`;
              if(this.groups){
                this.router.navigate(['/responsible/tournament',  this.groups[0].id_tournoi]);
              }
              else{
                this.router.navigate(['/responsible/tournament',  this.group.id_tournoi, 'groups']);
              }
              
              //console.log('user was created');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }

}