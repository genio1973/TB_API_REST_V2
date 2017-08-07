import { Component, OnInit, SimpleChanges } from '@angular/core';
import { Resultat } from "../../../../shared/models/resultat";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { ActivatedRoute } from "@angular/router";

@Component({
  selector: 'my-result-list',
  templateUrl: './result-list.component.html',
  styleUrls: ['./result-list.component.css']
})
export class ResultListComponent implements OnInit {

  tournamentId:number;
  results: Resultat[] = [];
  id_statut: number;
  displayType: string = 'heure';

  constructor(private service: PublicTournamentService, 
               private route: ActivatedRoute) { }

  ngOnInit() {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });

    // get all matchs's results
    this.service.getResultsByTournament(this.tournamentId)
      .subscribe(r => { this.results = r; console.log(r[0].score_sets); console.log(r) });
  }


  ngOnChanges(changes: SimpleChanges): void {
    
    switch(this.displayType){
      default:
      case 'groupe': this.matchsByGroups(); break;
      case 'terrain': this.matchsByPitches(); break;
      case 'heure' : this.matchsByHours(); break;
    }
  }


  pitchDisplayType(){
    this.displayType = 'terrain';
    this.matchsByPitches();
  }

  groupDisplayType(){
    this.displayType = 'groupe';
    this.matchsByGroups();
  }

  timeDisplayType(){
    this.displayType = 'heure';
    this.matchsByHours();
  }

  /**
   * regrouper par groupe
   */
  private matchsByGroups(){
    /*
    this.matchsGroupBy = [];
    this.groupsPlan.map(g => {
      if(g.planning.length>0) {
        this.matchsGroupBy.push(new MatchsGroupBy(g.planning));
      }
    });
    */
  }

  /**
   * Trié par heure de début du match
   */
  private matchsByHours(){
    /*
    this.matchsGroupBy = [];
    let matchs: MatchDetails[] = [];

    // Récupère tous le match en une seule liste
    this.groupsPlan.map( g =>{ g.planning.map(m => { matchs.push(m)})});

    // trie par heure
    matchs.sort((a: any, b: any) => {
      if (a.date_match < b.date_match) {
        return -1;
      } else if (a.date_match > b.date_match) {
        return 1;
      } else {
        return 0;
      }});
    
    // place dans l'attibut de classe les matchs
    this.matchsGroupBy.push(new MatchsGroupBy(matchs));
    this.matchsGroupBy[0].groupId = null;
    */
  }

  /**
   *  regrouper par terrain
   */
  private matchsByPitches(){
    /*
    this.matchsGroupBy = [];
    
    let terrainIds:number[]=[];
    for(let i=0; i < this.configSimul.nb_terrains; i++){ 
      terrainIds[i]=i+1;
    }

    //console.log("Debug");
    let planning: MatchDetails[] = [];
    terrainIds.map(numTerrain => {
      this.groupsPlan
        .map(g => { planning=g.planning.filter(m=> m.id_terrain == numTerrain);
                    if(planning.length > 0){
                      //console.log(planning);
                      this.matchsGroupBy.push(new MatchsGroupBy(planning));
                    }      
                })
    });
  */
  }



}
