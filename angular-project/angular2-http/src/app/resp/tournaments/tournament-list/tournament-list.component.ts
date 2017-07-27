import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../../shared/models/tournament";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";

@Component({
  selector: 'my-tournament-list',
  templateUrl: './tournament-list.component.html',
  styleUrls: ['./tournament-list.component.css']
})
export class TournamentListComponent implements OnInit {

    tournaments: Tournament[];
    test: string;  

    constructor( private tournamentService: RespTournamentService ){}

    ngOnInit(): void {
      this.tournamentService.getTournaments()
          .subscribe(
            tournaments => this.tournaments = tournaments);
    }

}



