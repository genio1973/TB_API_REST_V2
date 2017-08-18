import { Component, OnInit, SimpleChanges, Pipe, PipeTransform, OnDestroy } from '@angular/core';
import { Resultat } from "../../../../shared/models/resultat";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { ActivatedRoute } from "@angular/router";
import {AnonymousSubscription} from "rxjs/Subscription";
import {Observable} from 'rxjs/Rx';

@Component({
  selector: 'my-result-list',
  templateUrl: './result-list.component.html',
  styleUrls: ['./result-list.component.css'],
  
})

export class ResultListComponent implements OnInit, OnDestroy {
  arrayOfKeys;
  timerSubscription: AnonymousSubscription;
  tournamentId:number;
  results: Resultat[] = [];
  displayResults: any = [];
  id_statut: number;
  displayType: string = 'Horaire';
  responsibleRoute: boolean = true;

  constructor(private service: PublicTournamentService, 
               private route: ActivatedRoute) { }

  ngOnInit() {
    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });
      
    // Detect if its a public route or not
    if(this.route.pathFromRoot[1].snapshot.url[0].path === 'public'){
      this.responsibleRoute = false;
    }

    // get all matchs's results
    this.service.getResultsByTournament(this.tournamentId)
      .subscribe(r => { this.results = r;
                        this.sortingByHours(this.results);
                        this.displayResults.push({nameBlock:'Horaire', matchs:this.results});
                        this.refreshData();
                       });
  }

  ngOnDestroy(): void {
   if (this.timerSubscription) {
          this.timerSubscription.unsubscribe();
      }
  }

  ngOnChanges(changes: SimpleChanges): void {
    this.changeDiplayView();
  }


  /**
   * Select an another view mode
   */
   private changeDiplayView(){
    
    switch(this.displayType){
      default:
      case 'Groupe': this.groupDisplayType(); break;
      case 'Terrain': this.pitchDisplayType(); break;
      case 'Horaire' : this.timeDisplayType(); break;
    }    
  }


  /**
   * Display match by ptiches
   */
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
      this.displayResults.push({nameBlock:nom_terrain, matchs: this.results.filter(m => m.nom_terrain == nom_terrain)});
    });
  }

  /**
   * Display match by group
   */
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
      this.displayResults.push({nameBlock:nom_groupe, matchs: this.results.filter(m => m.nom_groupe == nom_groupe)});     
    });

  }

  /**
   * Display matchs by hours
   */
  private timeDisplayType(){
    this.displayType = 'Horaire';
    this.displayResults = [];
    this.displayResults.push({nameBlock:'Horaire', matchs:this.results});
  }

  /**
   * sorting match'results by date and hour
   * @param results
   */
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



  private refreshData(): void {
    // get all matchs's results
    this.service.getResultsByTournament(this.tournamentId)
      .subscribe(r => { this.results = r;
                        this.sortingByHours(this.results);
                        this.displayResults.push({nameBlock:'Horaire', matchs:this.results});
                        this.changeDiplayView();
                        this.subscribeToData();
                       });
  }

  private subscribeToData(): void {
    // Toutes les 60 secondes
    this.timerSubscription = Observable.timer(60000).first().subscribe(() => this.refreshData());
  }

}
