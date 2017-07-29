import { Component, OnInit } from '@angular/core';
import { Team } from "../../../../shared/models/team";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { Router, ActivatedRoute } from "@angular/router";

@Component({
  selector: 'my-team-edit',
  templateUrl: './team-edit.component.html',
  styleUrls: ['./team-edit.component.css']
})
export class TeamEditComponent implements OnInit {
    team: Team = { id_equipe: null, nom_equipe:'', niveau:'', nb_pts:0 ,id_groupe: 0};
    teamId: number;

    tournamentId: number;
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

      this.publicService
          .getTeam(this.teamId)
          .subscribe(team => this.team = team);
    }

    deleteTeam(){

    }


    updateteam(){
        this.errorMessage = '';
        this.successMessage = '';
        console.log(this.team);

        this.respService.updateTeam(this.team)
          .subscribe(
            user => {
              this.successMessage = 'Tournoi a été mis à jour.';
              this.router.navigate([`/responsible/tournament/${this.tournamentId}/teams/${this.team.id_equipe}`]);
              //console.log('user was created');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }

}