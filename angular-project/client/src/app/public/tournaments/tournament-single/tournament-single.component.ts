import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { Tournament } from "../../../shared/models/tournament";
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";

@Component({
  selector: 'my-tournament-single',
  templateUrl: `./tournament-single.component.html`,
  styleUrls: ['./tournament-single.component.css']
})
export class TournamentSingleComponent implements OnInit {

    tournament: Tournament;

    constructor(private tournamentService: PublicTournamentService,
                private router: Router,
                private route: ActivatedRoute) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.tournamentService
        .getTournament(id)
        .subscribe(tournament => this.tournament = tournament);
    }

    
}





