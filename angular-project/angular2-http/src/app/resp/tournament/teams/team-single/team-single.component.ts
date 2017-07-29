import { Component, OnInit } from '@angular/core';
import { Team } from "../../../../shared/models/team";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { Router, ActivatedRoute } from "@angular/router";

@Component({
  selector: 'my-team-single',
  templateUrl: './team-single.component.html',
  styleUrls: ['./team-single.component.css']
})
export class TeamSingleComponent implements OnInit {

    team: Team;
    teamId: number;
    tournamentId: number;

    errorMessage = '';
    successMessage = '';

    constructor(private service: PublicTournamentService,
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
      .subscribe( team => { this.team = team; console.log("Team : " + this.team.nom_equipe);} );       
    }
    
}