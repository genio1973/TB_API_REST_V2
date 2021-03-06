import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { MatchsPlan } from "../../../../shared/plannings/matchs-plan";
import { Match } from "../../../../shared/models/match";
import { MatchDetails } from "../../../../shared/models/match-details";
import { MatchsGroupBy } from "../../../../shared/plannings/matchs-group-by";
import { ConfigSimul } from "../../../../shared/plannings/config-simul";
import { SimulDataService } from "../../../../shared/services/simul-data.service";
import { Router, ActivatedRoute } from "@angular/router";
import { Team } from "../../../../shared/models/team";

@Component({
  selector: 'my-matchs',
  templateUrl: './matchs.component.html',
  styleUrls: ['./matchs.component.css']
})
export class MatchsComponent implements OnChanges {

  @Input() groupsPlan: MatchsPlan[] = [];
  @Input() configSimul: ConfigSimul;
  tournamentId:number;
  displayType: string = 'groupe';
  matchsGroupBy: MatchsGroupBy[] = [];

  ngOnInit(): void {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });
  }
    
  constructor(private simulDataService: SimulDataService,
              private router: Router,
              private route: ActivatedRoute) { }

  ngOnChanges(changes: SimpleChanges): void {
    
    switch(this.displayType){
      default:
      case 'groupe': this.matchsByGroups(); break;
      case 'terrain': this.matchsByPitches(); break;
      case 'heure' : this.matchsByHours(); break;
    }
  }


  newDragDropSimul(){
    //this.simulDataService.changeGroupsPlan(this.groupsPlan);
    
    this.matchsByPitches();
    this.simulDataService.changeGroupsPlan(this.matchsGroupBy);
    this.router.navigate(['/responsible/tournament',  this.tournamentId, 'simul-edit']);
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
    let equipeHomeVide: Team = { nom_equipe: 'match', id_equipe:-1, id_groupe: -1 };
    let equipeVisiteurVide: Team = { nom_equipe: 'Tampon', id_equipe:-1, id_groupe: -1 };
    let matchVide: MatchDetails = {equipe_home:equipeHomeVide, equipe_visiteur:equipeVisiteurVide, id_terrain:0};
    

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
      //console.log(plan);
      if(plan.length != 0){
        this.matchsGroupBy.push(new MatchsGroupBy(plan, `Terrrain ${numTerrain}`));
      }
      else{
        // If no match on this pitch add an temporary empty match
        matchVide.id_terrain = numTerrain;
        plan.push(matchVide);
        this.matchsGroupBy.push(new MatchsGroupBy(plan, `Terrrain ${numTerrain}`));        
      }
    })
      
  }

}