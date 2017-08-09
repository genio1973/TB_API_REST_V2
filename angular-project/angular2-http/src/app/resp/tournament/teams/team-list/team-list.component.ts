import { Component, OnInit, Input } from '@angular/core';
import { Group } from "../../../../shared/models/group";
import { ActivatedRoute, Router, RouterStateSnapshot } from "@angular/router";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { Team } from "../../../../shared/models/team";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { Coach } from "../../../../shared/models/coach";
import { Tournament } from "../../../../shared/models/tournament";

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
    tournament: Tournament;
    teams: Team[];  
    coachs: Coach[];
    groups: Group[];
    responsibleRoute:boolean = true;

    constructor( private service: PublicTournamentService,
                 private respService: RespTournamentService,
                 private router: Router,
                 private route: ActivatedRoute ){}

    ngOnInit(): void {
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
        console.log(params['idtournoi']);
        this.tournamentId = params['idtournoi'];
      });

      // Detect if its a public route or not
      //console.log(this.route.pathFromRoot[1].snapshot.url[0].path);
      if(this.route.pathFromRoot[1].snapshot.url[0].path === 'public'){
        this.responsibleRoute = false;
      }

      this.service.getTournament(this.tournamentId)
          .subscribe( t => this.tournament = t);

      if(this.group) {
        this.getTeamsInGroupInfo();
      } else {
        this.getAllTeamsInfo();
      }
    }
       

    /**
     * Get all groups ans their informations
     */
    private getTeamsInGroupInfo(){
      this.service
      //.getTeamsGroup(this.group.id_groupe)
      .getTournamentTeams(this.tournamentId)
      .subscribe(teams => {
        //this.teams = teams;
        if(teams){
          this.teams = teams.filter(t => t.id_groupe == this.group.id_groupe);
          }
        },
        err => {
          this.errorMessage = `Pas d'équipes à récupérer ou alors... ${err}`;
        });
    }

  /**
   * Get all teams ans their informations
   */
  private getAllTeamsInfo(){
      this.service
          .getTournamentTeams(this.tournamentId)
          .subscribe(teams => this.teams = teams,
            err => {
              this.errorMessage = `Pas d'équipes à récupérer ou alors... ${err}`;
            });
      
        if(this.responsibleRoute){ this.respService
          .getCoachs()
          .subscribe(coachs => this.coachs = coachs); 
        }
        this.service
        .getGroupsTournament(this.tournamentId)
        .subscribe(groups => this.groups = groups); 
  }
}

