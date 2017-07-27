import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../../shared/models/tournament";
import { Router } from "@angular/router";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";

@Component({
  selector: 'app-tournament-create',
  templateUrl: './tournament-create.component.html',
  styleUrls: ['./tournament-create.component.css']
})
export class TournamentCreateComponent implements OnInit {


    tournament: Tournament; // =  { name:'', username:'',  avatar:''};
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private tournamentService: RespTournamentService,
                private router: Router) { }

    ngOnInit() {
      this.tournament = new Tournament();
    }

    createTournament(){
        this.errorMessage = '';
        this.successMessage = '';
        this.tournamentService.createTournament(this.tournament)
          .subscribe(
            tournament => {
              this.router.navigate(['/responsible/tournaments']);
            },
            err => {
              this.errorMessage = err;
            });
    }


}
