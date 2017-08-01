import { Component, OnInit } from '@angular/core';
import { Team } from "../../../../shared/models/team";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { Router, ActivatedRoute } from "@angular/router";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { Coach } from "../../../../shared/models/coach";
import { Group } from "../../../../shared/models/group";

@Component({
  selector: 'my-team-single',
  templateUrl: './team-single.component.html',
  styleUrls: ['./team-single.component.css']
})
export class TeamSingleComponent implements OnInit {

    team: Team;
    teamId: number;
    tournamentId: number;
    coachs: Coach[];
    coach: Coach;
    group: Group;
    groups: Group[];

    errorMessage = '';
    successMessage = '';

    constructor(private service: PublicTournamentService,
                private respService: RespTournamentService,
                private router: Router,
                private route: ActivatedRoute) { }

    ngOnInit() {
    // get the ids from the url
    this.teamId = this.route.snapshot.params['idteam'];
    this.route.pathFromRoot[2].params.subscribe(params => {
        this.tournamentId = params['idtournoi'];
      });

    console.log("idTeam"+this.teamId);

    this.service
      .getTeam(this.teamId)
      .subscribe(team => this.team = team);       

    this.respService
        .getCoachs()
        .subscribe(coachs => {
          this.coachs = coachs;
          this.coach = coachs.find(c => c.id_personne == this.team.id_personne);
         } ); 

      this.service
      .getGroupsTournament(this.tournamentId)
      .subscribe(groups => {
        this.groups = groups;
        this.group = groups.find( g => g.id_groupe == this.team.id_groupe)
      }); 


    }
    
    
}