import { Component, OnInit, SimpleChanges, OnDestroy } from '@angular/core';
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { ActivatedRoute } from "@angular/router";
import { Ranking } from "../../../../shared/models/ranking";
import {AnonymousSubscription} from "rxjs/Subscription";
import {Observable} from 'rxjs/Rx';

@Component({
  selector: 'my-ranking-list',
  templateUrl: './ranking-list.component.html',
  styleUrls: ['./ranking-list.component.css']
})

export class RankingListComponent implements OnInit, OnDestroy  {

  tournamentId:number;
  timerSubscription: AnonymousSubscription;
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
      .subscribe(r => {
        this.displayRankings = r;
        this.refreshData();
      });
  }


  ngOnDestroy(): void {
      if (this.timerSubscription) {
          this.timerSubscription.unsubscribe();
      }
  }


  private refreshData(): void {
      // get all group's rankings
      this.service.getRankingsByTournament(this.tournamentId)
        .subscribe(r => {
          this.displayRankings = r;
          this.subscribeToData();
          console.log('REFRESH');
        });
  }

  private subscribeToData(): void {
    // Toutes les 60 secondes
    this.timerSubscription = Observable.timer(60000).first().subscribe(() => this.refreshData());
  }

}
