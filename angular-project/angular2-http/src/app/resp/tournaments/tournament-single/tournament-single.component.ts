import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../../shared/models/tournament";
import { Router, ActivatedRoute } from "@angular/router";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";

@Component({
  selector: 'app-tournament-single',
  templateUrl: `./tournament-single.component.html`,
  styleUrls: ['./tournament-single.component.css']
})
export class TournamentSingleComponent implements OnInit {
    tournament: Tournament;
    errorMessage = '';
    successMessage = '';

    constructor(private tournamentService: RespTournamentService,
                private router: Router,
                private route: ActivatedRoute) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.tournamentService
        .getTournament(id)
        .subscribe(tournament => this.tournament = tournament);
    }


    /**
     * Delete a tournament
     */
        deleteTournament(){
        this.errorMessage = '';
        this.successMessage = '';
        this.tournamentService.deleteTournament(this.tournament.id)
          .subscribe(
            data => {
              this.successMessage = 'Tournament was deleted.';
              this.router.navigate(['/responsible/tournaments']);
            } ,
            err => {
              this.errorMessage = err;
            });
    }

}
