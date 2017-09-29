import { Component, OnInit, ViewChild } from '@angular/core';
import { SimulDataService } from "../../../../shared/services/simul-data.service";
import { MatchsPlan } from "../../../../shared/plannings/matchs-plan";
import { DragulaService } from "../../../../../../node_modules/ng2-dragula/ng2-dragula";
import { Team } from "../../../../shared/models/team";
import { Match } from "../../../../shared/models/match";
import { MatchDetails } from "../../../../shared/models/match-details";
import { MatchsGroupBy } from "../../../../shared/plannings/matchs-group-by";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { Pitch } from "../../../../shared/models/pitch";
import { ApiResponse } from "../../../../shared/models/api-response";
import { Tournament } from "../../../../shared/models/tournament";
import { Router, ActivatedRoute } from "@angular/router";


@Component({
  selector: 'my-simulation-edit',
  templateUrl: './simulation-edit.component.html',
  styleUrls: ['./simulation-edit.component.css'],
  viewProviders: [DragulaService]
})

export class SimulationEditComponent implements OnInit {

  errorMultiTeamMessage:string = '';
  errorConflictMessage:string = '';
  groupsPlanOld: MatchsPlan[] = [];
  groupsPlan: MatchsGroupBy[] = [];
  pitchesPlan: PitchPlan[] = [];
  
  hours: Date [] = [];
  isTeamConflict: boolean = false;
  isMultiTeamInBox: boolean = false;
  tournament: Tournament;
  tournamentId: number;

  constructor(private simulDataService: SimulDataService,
              private dragulaService: DragulaService,
              private respService: RespTournamentService,
              private router: Router, 
              private route: ActivatedRoute ) {
    
    this.dragulaService.setOptions('match-bag', {
      invalid: (el, handle) => el.classList.contains('donotdrag'),
      //copy: true
    });
  }



  ngOnInit() {

    // get the id from the url
    this.route.pathFromRoot[2].params.subscribe(params => {
      this.tournamentId = params['idtournoi'];
    });

    // get the current tournament
    this.respService.getTournament(this.tournamentId).subscribe(t => { this.tournament = t;});

    // get the planning on each pitch from simulation service
    this.simulDataService.currentGroupsPlanSource.subscribe ( groupsPlan => {
    
      this.groupsPlan = groupsPlan;
      // get the number of time's matchs
      // consider the pitch with the most number of match in its planning
      let greatherPitchName: string = ''; // = this.groupsPlan[0].nameBlock;
      let greatherPitchLength: number = 0;

      
      // Get all match to made a drag n'drop system. A drag'n drop bag contains more matchs. (Dragula need it !)
      let i = 0;
      this.groupsPlan.map( g => {
        if(g.planning.length > greatherPitchLength) { greatherPitchName =  g.nameBlock; greatherPitchLength = g.planning.length}

        this.pitchesPlan.push(new PitchPlan());
        g.planning.map ( m => {
          let listMatch = new ListMatch();
          if(m.equipe_home.id_equipe != -1) // si ce n'est pas le match tampon alors...
            listMatch.matchs.push(m);
          
          this.pitchesPlan[i].planning.push(listMatch);
        })
        this.pitchesPlan[i++].name = g.nameBlock;
      });

      // create an array with the all time's match 
      this.groupsPlan
        .find(g => g.nameBlock == greatherPitchName)
        .planning.map( m => {
          this.hours.push(m.date_match);
        });

      this.addMatchEndPitches();
      this.checkConflict();
      this.checkMultiTeams();
    });

    
    this.dragulaService.drag.subscribe((value:any) => {
      //console.log(`drag: ${value[0]}`); // value[0] will always be bag name
      this.onDrag(value.slice(1));
      this.checkConflict();
      this.checkMultiTeams();
    });

    this.dragulaService.drop.subscribe((value:any) => {
      // console.log(`drop: ${value[0]}`);
      this.onDrop(value.slice(1));
      this.checkConflict();
      this.checkMultiTeams();
    });

    this.dragulaService.over.subscribe((value:any) => {
      // console.log(`over: ${value[0]}`);
      this.onOver(value.slice(1));
      this.checkConflict();
      this.checkMultiTeams();
    });
    
    this.dragulaService.out.subscribe((value:any) => {
      // console.log(`out: ${value[0]}`);
      this.onOut(value.slice(1));
      this.checkConflict();
      this.checkMultiTeams();
    });

  }

  private hasClass(el:any, name:string):any {
    return new RegExp('(?:^|\\s+)' + name + '(?:\\s+|$)').test(el.className);
  }

  private addClass(el:any, name:string):void {
    if (!this.hasClass(el, name)) {
      el.className = el.className ? [el.className, name].join(' ') : name;
    }
  }

  private removeClass(el:any, name:string):void {
    if (this.hasClass(el, name)) {
      el.className = el.className.replace(new RegExp('(?:^|\\s+)' + name + '(?:\\s+|$)', 'g'), '');
    }
  }

  private onDrag(args:any):void {
    let [e] = args;
    this.removeClass(e, 'ex-moved');
  }

  private onDrop(args:any):void {
    let [e] = args;
    this.addClass(e, 'ex-moved');
    
  }

  private onOver(args:any):void {
    let [el] = args;
    this.addClass(el, 'ex-over');
  }

  private onOut(args:any):void {
    let [el] = args;
    this.removeClass(el, 'ex-over');
  }

  /**
   * Add some empty match at the end of each pitches, if no match present
   */
  private addMatchEndPitches(){
    // for pitchs for those have less matchs in planning
    // Add some empty matchs
    this.pitchesPlan.map(p => {
        while(p.planning.length < this.hours.length){
          let listMatch = new ListMatch();
          p.planning.push(listMatch);
        }
    });
  }

  /**
   * Add new line in hours array
   */
  createNewHourLine(){
    this.hours.push(new Date(this.hours[this.hours.length-1].getTime() + this.hours[1].getTime() - this.hours[0].getTime())); 
    this.addMatchEndPitches();
  }

  /**
   * Check if a team plays once per playtime 
   * Return false if no conflict is present
   */
  private checkConflict(){

    this.errorConflictMessage = '';
    this.isTeamConflict = false;

    // get all matchs played on the different pitches on the same hour
    this.hours.map( (h, i) => {
      let matchs: MatchDetails[] = [];
      for( let indexPitch=0; indexPitch<this.pitchesPlan.length; indexPitch++){
        this.pitchesPlan[indexPitch].planning[i].matchs.map(m =>{
          m.isConflict = false;
          matchs.push(m);
        });

        // A team is present more once ?
        let matchUnique: MatchDetails[] = [];
        let uniqueTeams: Team[] = [];
        matchs.map( m => {
          if(!uniqueTeams.includes(m.equipe_home)){
            uniqueTeams.push(m.equipe_home);
          }
          else{
            m.isConflict = true;
            this.isTeamConflict = true;
          }
          if(!uniqueTeams.includes(m.equipe_visiteur)){
            uniqueTeams.push(m.equipe_visiteur);
          }
          else{
            m.isConflict = true;
            this.isTeamConflict = true;
          }

          // only if there is an self-referee
          /*
          if(m.equipe_arbitre){
            if(!uniqueTeams.includes(m.equipe_arbitre)){
              uniqueTeams.push(m.equipe_arbitre);
            }
            else{
              m.isConflict = true;
              this.isTeamConflict = true;
            } 
          }
          */         
        })
      }
    });

    if(this.isTeamConflict) {
      this.errorConflictMessage = 'Une ou des équipes sont en conflit';
    }
  }


  /**
   * Set hours and the pitch for each match. the hours come from hours array
   */
  private setMatchHoursAndPitches(){

    // get all matchs played on the different pitches on the same hour
    this.hours.map( (h, i) => {
      for( let indexPitch=0; indexPitch<this.pitchesPlan.length; indexPitch++){
        this.pitchesPlan[indexPitch].planning[i].matchs.map(m =>{
          m.date_match = h;
          m.id_terrain = indexPitch;
        });
      }
    });
  }


  /**
   * Check if a team plays once per playtime 
   * Return false if no conflict is present
   */
  private checkMultiTeams(){

    this.errorMultiTeamMessage = '';
    this.isMultiTeamInBox = false;

    // find if there'are more than one match in the each match box.
    // search for each hours, and for each pitch
    this.hours.map( (h, i) => {
      let matchs: MatchDetails[] = [];
      for( let indexPitch=0; indexPitch<this.pitchesPlan.length; indexPitch++){
        if( this.pitchesPlan[indexPitch].planning[i].matchs.length > 1){
          this.isMultiTeamInBox = true;
        }
      }
    });

    if(this.isMultiTeamInBox){
      this.errorMultiTeamMessage = 'Plusieurs matchs sur le même terrain à la même heure !';
    }
  }

  matchs: MatchDetails[] = [];
  errorMessage:string = '';
  successMessage:string = '';

  /**
   * Insert the planning data (with tournament) in database
   */
  insertDataMatchsToDB(){

    // set hours for each match
    this.setMatchHoursAndPitches();

    // Get all matchs in only one list
    this.matchs = [];
    this.pitchesPlan.map( p => {
      p.planning.map( matchsList => { 
        if(matchsList.matchs[0]) {
          this.matchs.push(matchsList.matchs[0])
        };
      })
    });

    //Insert les nouveau terrains et les matchs   
    this.insertPitchesAndMatchsDB();
    
    // Insert les matchs
    //this.insertMatchsDB();
  }

  
  /**
   * Insert les nouveaux terrains  
   */
  private insertPitchesAndMatchsDB(){
    let id_first_pitch_inserted: number = 0;
    let pitches: Pitch[] = [];

    // crée autant de terrain qu'il est configuré par l'utilistateur

    for(let i=0; i<this.pitchesPlan.length;i++){
      let ter: Pitch = {id_terrain:0, nom_terrain:'A'};
      ter.nom_terrain = 'Ter_' + (i+1);
      pitches.push(ter);
    }

    // insert in the db all the necessary pitches
    this.respService.createPitches(pitches)
    .map(res => {return res})
    .subscribe(
      res => {
        let apiResp : ApiResponse = res;
        id_first_pitch_inserted = apiResp.result.id_premier_insert;
        pitches.map(t => t.id_terrain = id_first_pitch_inserted++);

        // update in planning all the id pitches (from inserted id)
        this.matchs.map( m => { m.id_terrain = pitches[m.id_terrain].id_terrain; });
        this.insertMatchsDB();
      },
      err => {
        this.errorMessage = err;
      });
  }

  /**
   * Insert les matchs dans la bd, avec un délai avant de créer 
   */
  private insertMatchsDB(){
    let matchsToPost: Match[] = [];
    
    //setTimeout(() => {
    this.matchs.map(m => { matchsToPost.push(this.toMatchFieldCreation(m))});
      this.respService.createMatchs(matchsToPost)
      .subscribe(
        res => {
          this.successMessage = "Création terminée";
          // Le tournoi a un nouveau statut : OUVERT 
          this.tournament.id_statut = 2;
          this.respService.updateTournament(this.tournament)
              .subscribe(
                res => {
                  this.successMessage += "Le tournoi passe en mode ouvert";
                }
              )
          this.router.navigate(['/responsible/tournaments/list']);
        },
        err => {
          this.errorMessage = err;
        });
    //}, 2000);
  }

  /**
   * Format match to insert in db
   * @param m
   */
  private toMatchFieldCreation(m: MatchDetails): Match {
    let match: Match = {
              date_match: `${m.date_match.getFullYear()}-${m.date_match.getMonth()+1}-${m.date_match.getDate()}`,
              heure: `${m.date_match.getHours()}:${m.date_match.getMinutes()}:00`,
              id_equipe_home: m.equipe_home.id_equipe,
              id_equipe_visiteur: m.equipe_visiteur.id_equipe,
              id_terrain: m.id_terrain,
              statut: m.statut,
      }
    if(m.equipe_arbitre){
      match.id_equipe_arbitre = m.equipe_arbitre.id_equipe;
    }
    return match;
  }

} // class ending here

// class used in this component only
export class ListMatch{
  matchs: MatchDetails[] = [];
  constructor(){
  }
}

export class PitchPlan{
  planning: ListMatch[] = [];
  name: string;

  constructor(){
  }
}