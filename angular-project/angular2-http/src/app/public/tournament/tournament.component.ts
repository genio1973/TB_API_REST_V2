import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../shared/models/tournament";
import { PublicTournamentService } from "../../shared/services/public-tournament.service";
import { Router, ActivatedRoute } from "@angular/router";

@Component({
  selector: 'my-public-tournament',
  templateUrl: 'tournament.component.html',
  styleUrls: ['./tournament.component.css']
})
export class TournamentComponent implements OnInit {


  tournament: Tournament;
  tournamentId: number;

  constructor(private service: PublicTournamentService,
              private router: Router,
              private route: ActivatedRoute) { }

  ngOnInit() {
      this.tournamentId = this.route.snapshot.params['idtournoi'];
      this.service
        .getTournament(this.tournamentId)
        .subscribe(tournament => {this.tournament = tournament; });
  }

}