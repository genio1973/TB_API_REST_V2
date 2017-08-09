import { Component, OnInit, SimpleChanges } from '@angular/core';
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { ActivatedRoute } from "@angular/router";
import { Ranking } from "../../../../shared/models/ranking";

@Component({
  selector: 'my-ranking-list',
  templateUrl: './ranking-list.component.html',
  styleUrls: ['./ranking-list.component.css']
})
export class RankingListComponent implements OnInit {
arrayOfKeys;

  tournamentId:number;
 
  displayRankings: any = [];
  id_statut: number;

  constructor(private service: PublicTournamentService, 
               private route: ActivatedRoute) { }

  ngOnInit() {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });
      

    // get all group's rankings
    this.service.getRankingsByTournament(this.tournamentId)
      .subscribe(r => this.displayRankings = r);
  }


  ngOnChanges(changes: SimpleChanges): void {
       
  }

}
