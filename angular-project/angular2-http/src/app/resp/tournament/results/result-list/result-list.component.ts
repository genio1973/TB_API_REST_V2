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
  displayResults: any = [];
  id_statut: number;
  displayType: string = 'Horaire';

  constructor(private service: PublicTournamentService, 
               private route: ActivatedRoute) { }

  ngOnInit() {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });

    // get all matchs's results
    this.service.getResultsByTournament(this.tournamentId)
      .subscribe(r => { this.results = r; this.displayResults.push(this.results); });
  }


  ngOnChanges(changes: SimpleChanges): void {
    
    switch(this.displayType){
      default:
      case 'Groupe': this.groupDisplayType(); break;
      case 'Terrain': this.pitchDisplayType(); break;
      case 'Horaire' : this.timeDisplayType(); break;
    }
    this.displayResults.map(g=>this.sortingByHours(g));
  }


  private pitchDisplayType(){

    this.displayType = 'Terrain';
    this.displayResults = [];
    
    // Get all id pitches
    let terrains:string[] = [];
    this.results.map(m => {
      // add the id pitc if not alreay int the array
      if(terrains.indexOf(m.nom_terrain) < 0){
        terrains.push(m.nom_terrain);
      }
    });

    //for each pitch get the matchs and put it in a specific group
    terrains.map(nom_terrain => {
      this.displayResults.push(this.results.filter(m => m.nom_terrain == nom_terrain));
    });
  }

  private groupDisplayType(){
    this.displayType = 'Groupe';
    this.displayResults = [];
    
    // Get all id pitches
    let groups:string[] = [];
    this.results.map(m => {
      // add the id pitc if not alreay int the array
      if(groups.indexOf(m.nom_groupe) < 0){
        groups.push(m.nom_groupe);
      }
    });

    //for each pitch get the matchs and put it in a specific group
    groups.map(nom_groupe => {
      this.displayResults.push(this.results.filter(m => m.nom_groupe == nom_groupe));      
    });

  }

  private timeDisplayType(){
    this.displayType = 'Horaire';
    this.displayResults = [];
    this.displayResults.push(this.results); 
    console.log(this.displayResults);
    console.log(this.results);

  }

  private sortingByHours(results: Resultat[]){

    results.sort((a, b) => {
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
