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
  rankings: Ranking[] = [];
  displayRankings: any = [];
  id_statut: number;

  constructor(private service: PublicTournamentService, 
               private route: ActivatedRoute) { }

  ngOnInit() {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });
      

    // get all matchs's rankings
    this.service.getRankingsByTournament(this.tournamentId)
      .subscribe(r => { this.displayRankings = r;
                        //this.sortingByHours(this.rankings);
                        this.displayRankings = r;
                       });

  }


  ngOnChanges(changes: SimpleChanges): void {
       
  }



  /**
   * sorting match'rankings by date and hour
   * @param rankings
   */
  /*
  private sortingByHours(rankings: Ranking[]){

    rankings.sort((a, b) => {
        let aNew: Date = new Date(`${a.date_match}T${a.heure}`);
        let bNew: Date = new Date(`${b.date_match}T${b.heure}`);
        //bNew: Date = 
        if (aNew.getTime() < bNew.getTime()) {
          return -1;
        } else if (aNew.getTime() > bNew.getTime()) {
          return 1;
        } else {
          return 0;
        }});
  }
*/
}
