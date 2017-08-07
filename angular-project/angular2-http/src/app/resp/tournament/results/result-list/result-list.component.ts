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
      .subscribe(r => this.results = r);
  }


  ngOnChanges(changes: SimpleChanges): void {
    
    switch(this.displayType){
      default:
      case 'groupe': this.groupDisplayType(); break;
      case 'terrain': this.pitchDisplayType(); break;
      case 'heure' : this.timeDisplayType(); break;
    }
  }


  pitchDisplayType(){
    this.displayType = 'terrain';
    this.results.sort((a, b) => {
        if (a.nom_terrain < b.nom_terrain) {
          return -1;
        } else if (a.nom_terrain > b.nom_terrain) {
          return 1;
        } else {
          return 0;
        }});
  }

  groupDisplayType(){
    this.displayType = 'groupe';
    this.results.sort((a, b) => {
        if (a.nom_groupe < b.nom_groupe) {
          return -1;
        } else if (a.nom_groupe > b.nom_groupe) {
          return 1;
        } else {
          return 0;
        }});
  }

  timeDisplayType(){
    this.displayType = 'heure';
    this.results.sort((a, b) => {
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

}
