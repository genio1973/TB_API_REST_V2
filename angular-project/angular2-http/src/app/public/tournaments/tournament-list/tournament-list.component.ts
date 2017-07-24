import { Component, OnInit } from '@angular/core';
import { TournamentService } from "../../../shared/services/tournament.service";
import { Tournament } from "../../../shared/models/tournament";

@Component({
  selector: 'my-tournament-list',
  templateUrl: './tournament-list.component.html',
  styleUrls: ['./tournament-list.component.css']
})
export class TournamentListComponent implements OnInit {

    tournaments: Tournament[];
    test: string;  

    constructor( private tournamentService: TournamentService ){}

    ngOnInit(): void {
      this.tournamentService.getTournaments()
          .subscribe(
            tournaments => this.tournaments = tournaments);
    }

}



