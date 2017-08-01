import { Component, OnInit } from '@angular/core';
import { Tournament } from "../../shared/models/tournament";
import { Router, ActivatedRoute } from "@angular/router";
import { RespTournamentService } from "../../shared/services/resp.tournament.service";

@Component({
  selector: 'my-tournament',
  templateUrl: './tournament.component.html',
  styleUrls: ['./tournament.component.css']
})

export class TournamentComponent implements OnInit {

  tournament: Tournament;
  tournamentId: number;

  constructor(private service: RespTournamentService,
              private router: Router,
              private route: ActivatedRoute) { }

  ngOnInit() {
      this.tournamentId = this.route.snapshot.params['idtournoi'];
      this.service
        .getTournament(this.tournamentId)
        .subscribe(tournament => {this.tournament = tournament; });
  }

}
