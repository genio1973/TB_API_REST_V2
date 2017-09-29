import { Component, OnInit } from '@angular/core';
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { Tournament } from "../../../shared/models/tournament";

@Component({
  selector: 'my-tournament-list',
  templateUrl: './tournament-list.component.html',
  styleUrls: ['./tournament-list.component.css']
})
export class TournamentListComponent implements OnInit {

    tournaments: Tournament[];
    test: string;  

    constructor( private tournamentService: PublicTournamentService ){}

    ngOnInit(): void {
      this.tournamentService.getTournaments()
          .subscribe(
            tournaments =>  {
              this.tournaments = tournaments.filter( t => t.statut_tournoi != 'NOUVEAU');
              //console.log(this.tournaments);
          });
    }
}



