import { Component, OnInit, Input } from '@angular/core';
import { Group } from "../../../../shared/models/group";
import { ActivatedRoute, Router } from "@angular/router";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { Team } from "../../../../shared/models/team";

@Component({
  selector: 'my-team-list',
  templateUrl: './team-list.component.html',
  styleUrls: ['./team-list.component.css']
})

export class TeamListComponent implements OnInit {
    @Input() group: Group;
    @Input('tournamentId') tournamentId: number;
    errorMessage = '';
    successMessage = '';
    teams: Team[];  

    constructor( private service: PublicTournamentService,
                 private router: Router,
                 private route: ActivatedRoute ){}

    ngOnInit(): void {
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
        console.log(params['idtournoi']);
        this.tournamentId = params['idtournoi'];
      });

      if(this.group){
        this.service
            //.getTeamsGroup(this.group.id_groupe)
            .getTournamentTeams(this.tournamentId)
            .subscribe(teams => {
              //this.teams = teams;
              this.teams = teams.filter(t => t.id_groupe == this.group.id_groupe);
              },
              err => {
                this.errorMessage = `Pas d'équipes à récupérer ou alors... ${err}`;
              });
      }
      else{
        this.service
            .getTournamentTeams(this.tournamentId)
            .subscribe(teams => {
                this.teams = teams;
              },
              err => {
                this.errorMessage = `Pas d'équipes à récupérer ou alors... ${err}`;
              });
      }
    }
          
  /**
   * Clear all messages after 5 sec
   */
  clearMessages(){
    
    setTimeout(() => {
      this.errorMessage = '';
      this.successMessage = '';  
    }, 5000);
  }
}

