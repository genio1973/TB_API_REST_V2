import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-simulation',
  templateUrl: './simulation.component.html',
  styleUrls: ['./simulation.component.css']
})
export class SimulationComponent implements OnInit {

  //d: Date = new Date("2017-08-17T08:30:00+0100");
  d: Date = new Date("2017-08-17T08:30:00");
  configSimul: ConfigSimul;

  
  constructor() { }

  ngOnInit() {
    this.configSimul = {tournoi_date: this.d, heure_debut_h: this.d.getHours(), heure_debut_min: this.d.getMinutes(), match_duree: 5, matchs_meme_terrain: true, nb_terrains:2};
  }

  simulPlanning(){

  }

  toggleMatchs_meme_terrain(){
    this.configSimul.matchs_meme_terrain = !this.configSimul.matchs_meme_terrain; 
  }
}

class ConfigSimul{
  tournoi_date: Date;
  heure_debut_h: number;
  heure_debut_min: number;
  match_duree: number;
  matchs_meme_terrain: boolean;
  nb_terrains: number;


}


