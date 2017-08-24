import { Component, OnInit, Input } from '@angular/core';
import { Resultat } from "../../../../shared/models/resultat";
import { ActivatedRoute, Router } from "@angular/router";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { ScoreSet } from "../../../../shared/models/score-set";
import { Match } from "../../../../shared/models/match";
import { SetMatch } from "../../../../shared/models/set-match";

@Component({
  selector: 'app-result-edit',
  templateUrl: './result-edit.component.html',
  styleUrls: ['./result-edit.component.css']
})
export class ResultEditComponent implements OnInit {
  
  result: Resultat;
  successMessage: string = '';
  errorMessage: string = '';
  statuts: string[] = ['', 'partiel', 'final'];
  tournamentId: number;


  constructor(  private service: PublicTournamentService,
                private respService: RespTournamentService,
                private router: Router,
                private route: ActivatedRoute) { }

  ngOnInit() {
    // get the id from the url
    let id_match: number;
    id_match = this.route.snapshot.params['idmatch'];

    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });

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


  updateResult(){

    this.errorMessage = '';
    this.successMessage = '';
    
    // delete old match's result (all sets) 
    this.deletePreMatchResult();

    // create new match's result
    this.createNewMatchResult();

    // update statut match
    // Wait to complete old scores deletion
    setTimeout(() => {
       this.updateMatchStatut();
    }, 1000);

    // if no erro go back to the result list
    if(this.errorMessage == ''){
      this.successMessage = 'Résultat mis à jour. Vous allez être redirigés sur la page des résulats';

      setTimeout(() => {
        this.router.navigate(['/responsible/tournament',  this.tournamentId, 'results','list']);
      }, 2000);
      
    }
  }

  /**
   * When sets value change, check if the statut have to be null or not
   */
    valuechange(){
      this.result.statut = '' ;
      let isValue: boolean = false;
      this.result.score_sets.map( s =>  { if((s.set[0] != '' && s.set[0] != null ) || ( s.set[1] != '' && s.set[1] != null )) { isValue = true;}} );

      if(isValue){
        this.result.statut = 'final' ;
      }

  }


  /**
   * delete old match's result (all sets) 
   */
  private deletePreMatchResult(){

    this.respService.deleteScore(this.result.id_match)
      .subscribe(
      data => {
      },
      err => {
        //this.errorMessage = err;
      });
  }

  /**
   * create new match's result
   */
  private createNewMatchResult(){
        // get sets (only if values are sets)
        let newScore: SetMatch[] = [];
        this.result.score_sets.map( s => {
          if(s.set[0] != '' && s.set[1] !='' ){
            let mySet: SetMatch = { score_home: parseInt(s.set[0]), score_visiteur: parseInt(s.set[1]), id_match: this.result.id_match};
            newScore.push(mySet);
          }
        });
        this.respService.createSets(newScore)
                  .subscribe(
                    data => {
                    },
                    err => {
                      this.errorMessage = err;
                    });
  }

  /**
   * update statut match
   */
  private updateMatchStatut(){
      
    let match: Match = { id_match: this.result.id_match,
                          statut: this.result.statut,
                          id_equipe_home: this.result.id_equipe_home, 
                          id_equipe_visiteur: this.result.id_equipe_visiteur };

    this.respService.updateMatch(match)
      .subscribe(
        data => {
        },
        err => {
          this.errorMessage = err;
        });
  }

}
