import { Component, OnInit, Input } from '@angular/core';
import { Resultat } from "../../../../shared/models/resultat";
import { ActivatedRoute } from "@angular/router";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { ScoreSet } from "../../../../shared/models/score-set";

@Component({
  selector: 'app-result-edit',
  templateUrl: './result-edit.component.html',
  styleUrls: ['./result-edit.component.css']
})
export class ResultEditComponent implements OnInit {
  
  result: Resultat;
  successMessag: string = '';
  errorMessage: string = '';
  statuts: string[] = ['', 'partiel', 'final'];


  constructor(  private service: PublicTournamentService,
                private respService: RespTournamentService,
                private route: ActivatedRoute) { }

  ngOnInit() {
    let id_match: number;
    // get the id from the url
    id_match = this.route.snapshot.params['idmatch'];

    // get the id from the url
    // let tournamentId;
    // this.route.pathFromRoot[2].params.subscribe(params => {
    //   tournamentId = params['idtournoi'];
    // });

    // get all matchs's result
    this.service.getMatchResult(id_match)
      .subscribe(r => { 
        this.result = r
        
        if(!this.result.score_sets){ // Not yet set, then init it !
          this.result.score_sets = []
        }

        // Add sets if not 6 are presents
        let missingSets: number;
        missingSets = this.result.score_sets ?  6 - this.result.score_sets.length : 6;

        //let missingSets: number = 6 - this.result.score_sets.length;
        for(let i=0; i<missingSets;i++){
          let scores: ScoreSet = new ScoreSet();
          scores.set = ['',''];
          this.result.score_sets.push(scores);
        }
      });
  }

}
