import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { ConfigSimul } from "../../../shared/plannings/ConfigSimul";
import { MatchsPlan } from "../../../shared/plannings/matchs-plan";
import { Match } from "../../../shared/models/match";
import { MatchDetails } from "../../../shared/models/match-details";

@Component({
  selector: 'my-matchs',
  templateUrl: './matchs.component.html',
  styleUrls: ['./matchs.component.css']
})
export class MatchsComponent implements OnChanges {

  @Input() configSimul: ConfigSimul = null;
  @Input() groupsPlan: MatchsPlan[] = [];
  matchs: MatchDetails[] = [];
  
  constructor() { }

  ngOnChanges(changes: SimpleChanges): void {
    
    //console.log(`DATE : ${date_heure}`);
    let terrainId: number = 0;

    //selon la configuration reçue, préparer les infos de chaque match
    this.groupsPlan.map( g =>{ let date_heure : Date = new Date(this.dateTimeStart());
                                terrainId++;
                                g.planning.map(m => {
                                    m.date_match = date_heure;
                                    m.statut = 'non jouée';
                                    m.id_terrain = terrainId;
                                    this.matchs.push(m);
                                    date_heure = new Date((new Date(date_heure)).getTime() + (this.configSimul.match_duree*60*1000));
                                  })
                              });
  }


  private dateTimeStart(): string{
    let hh = this.configSimul.heure_debut_h < 10 ? `0${this.configSimul.heure_debut_h}`:  this.configSimul.heure_debut_h;
    let min = this.configSimul.heure_debut_min < 10 ? `0${this.configSimul.heure_debut_min}`:  this.configSimul.heure_debut_min;
    return `${this.configSimul.tournoi_date}T${hh}:${min}:00`;
  }

}
