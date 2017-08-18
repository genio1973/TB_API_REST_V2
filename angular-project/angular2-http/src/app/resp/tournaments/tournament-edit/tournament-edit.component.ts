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
    id_statut_tmp: number;
    askToConfirm=false;

    constructor(private tournamentService: RespTournamentService,
                private route: ActivatedRoute,
                private router: Router) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.tournamentService
          .getTournament(id)
          .subscribe(tournament => { this.tournament = tournament; this.id_statut_tmp = tournament.id_statut;});

      this.tournamentService
          .getAllTounamentStatuts()
          .subscribe(status => this.statuts = status);
    }


    // ask to update tournament
    modifyStatut(){
      // if we try to back to new status
      if(this.tournament.id_statut == 1 ){
        this.askToConfirm = true;
      }

    }

    /**
     * Update ther tournament
     */
    updateTournament(){

        this.errorMessage = '';
        this.successMessage = '';

        // if we change the statut to new
        if(this.askToConfirm && this.tournament.id_statut == 1){
          this.tournamentService.deleteTournamentPitches(this.tournament.id)
            .subscribe();
            
          this.tournamentService.deleteTournamentMatchs(this.tournament.id)
            .subscribe();
        }
        
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

    /**
     * Delete a tournament
     */
    deleteTournament(){
        this.errorMessage = '';
        this.successMessage = '';

        if(this.tournament.id_statut > 1){
          this.errorMessage = 'Il faut dans un premier temps passé le tournoi au statut NOUVEAU. Ceci par sécurité';
          return;
        }

        this.tournamentService.deleteTournament(this.tournament.id)
          .subscribe(
            data => {
              this.successMessage = 'Tournoi supprimé.';
              this.router.navigate(['/responsible/tournaments/list']);
            } ,
            err => {
              this.errorMessage = err;
            });
    }
}
