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
  displayType: string = 'groupe';
  matchsGroupBy: MatchsGroupBy[] = [];
  
  constructor() { }

  ngOnChanges(changes: SimpleChanges): void {
    
    switch(this.displayType){
      default:
      case 'groupe': this.matchsByGroups(); break;
      case 'terrain': this.matchsByPitches(); break;
      case 'heure' : this.matchsByHours(); break;
    }
  }


  ngOnInit(): void {
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
    this.matchsGroupBy = [];
    this.groupsPlan.map(g => {
      if(g.planning.length>0) {
        this.matchsGroupBy.push(new MatchsGroupBy(g.planning, g.nameBlock));
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
    this.matchsGroupBy.push(new MatchsGroupBy(matchs, 'Horaire'));
    this.matchsGroupBy[0].groupId = null;
  }

  /**
   *  regrouper par terrain
   */
  private matchsByPitches(){
    this.matchsGroupBy = [];
    
    // Numerate the pitches from 1 to number of pitches
    let terrainIds:number[]=[];
    for(let i=0; i < this.configSimul.nb_terrains; i++){ 
      terrainIds[i]=i+1;
    }
  
    // Get all match in a array
    let myPlanning: MatchDetails[] = [];
    this.groupsPlan.map(g => g.planning.map( m => myPlanning.push(m) ));

    // Group match by pitch
    terrainIds.map(numTerrain => {
      let plan = myPlanning.filter(m=> m.id_terrain == numTerrain);
      this.matchsGroupBy.push(new MatchsGroupBy(plan, `Terrrain ${numTerrain}`));
    })
    
  }
}
