import { Component, OnInit } from '@angular/core';

import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { ActivatedRoute, Router } from "@angular/router";
import { StatutTournoi } from "../../../shared/models/statut-tournoi";
import { Tournament } from "../../../shared/models/tournament";
import { TournamentSimple } from "../../../shared/models/tournament-simple";

@Component({
  selector: 'app-tournament-edit',
  templateUrl: './tournament-edit.component.html',
  styleUrls: ['./tournament-edit.component.css']
})
export class TournamentEditComponent implements OnInit {
    tournament: Tournament;
    successMessage: string = '';
    errorMessage: string = '';
    needPasswordChange: boolean = false;
    statuts : StatutTournoi;

    constructor(private tournamentService: RespTournamentService,
                private route: ActivatedRoute,
                private router: Router) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.tournamentService
          .getTournament(id)
          .subscribe(tournament=> this.tournament = tournament);

      this.tournamentService
          .getAllTounamentStatuts()
          .subscribe(status => this.statuts = status);
    }

    /**
     * Update ther tournament
     */
    updateTournament(){

        this.errorMessage = '';
        this.successMessage = '';
        //let tournamentSimple : Tournament;
        //tournamentSimple = this.tournament; 
        
        this.tournamentService.updateTournament(this.tournament)
          .subscribe(
            data => {
              //this.successMessage = 'Tournament was updated.';
              this.router.navigate(['/responsible/tournaments/list']);
            },
            err => {
              this.errorMessage = err;
            });
    }

}
