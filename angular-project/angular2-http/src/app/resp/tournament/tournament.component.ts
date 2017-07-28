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

  private tournament: Tournament;

  constructor(private service: RespTournamentService,
              private router: Router,
              private route: ActivatedRoute) { }

  ngOnInit() {
      let id = this.route.snapshot.params['idtournoi'];
      this.service
        .getTournament(id)
        .subscribe(tournament => {this.tournament = tournament; console.log(tournament.date_debut)});
  }

}
