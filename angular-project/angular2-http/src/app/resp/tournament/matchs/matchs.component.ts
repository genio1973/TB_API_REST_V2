import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { MatchsPlan } from "../../../shared/plannings/matchs-plan";
import { Match } from "../../../shared/models/match";
import { MatchDetails } from "../../../shared/models/match-details";
import { MatchsGroupBy } from "../../../shared/plannings/matchs-group-by";
import { ConfigSimul } from "../../../shared/plannings/config-simul";

@Component({
  selector: 'my-matchs',
  templateUrl: './matchs.component.html',
  styleUrls: ['./matchs.component.css']
})
export class MatchsComponent implements OnChanges {

  @Input() groupsPlan: MatchsPlan[] = [];
  @Input() configSimul: ConfigSimul;
  displayByGroup: string = 'groupe';
  matchsGroupBy: MatchsGroupBy[] = [];
  
  constructor() { }

  ngOnChanges(changes: SimpleChanges): void {
    
    switch(this.displayByGroup){
      default:
      case 'groupe': this.matchsByGroups(); break;
      case 'terrain': this.matchsByPitches(); break;
      case 'heure' : this.matchsByHours(); break;
    }
  }


  ngOnInit(): void {
  }

  pitchDisplayType(){
    this.displayByGroup = 'terrain';
    this.matchsByPitches();
  }

  groupDisplayType(){
    this.displayByGroup = 'groupe';
    this.matchsByGroups();
  }

  timeDisplayType(){
    this.displayByGroup = 'heure';
    this.matchsByHours();
  }

  /**
   * regrouper par groupe
   */
  private matchsByGroups(){
    this.matchsGroupBy = [];
    this.groupsPlan.map(g => {
      if(g.planning.length>0) {
        this.matchsGroupBy.push(new MatchsGroupBy(g.planning));
      }
    });
  }

  /**
   * Trié par heure de début du match
   */
  private matchsByHours(){
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
  }

  /**
   *  regrouper par terrain
   */
  private matchsByPitches(){
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
  }
}
