import { Component, OnInit, ViewChild } from '@angular/core';
import { SimulDataService } from "../../../../shared/services/simul-data.service";
import { MatchsPlan } from "../../../../shared/plannings/matchs-plan";
import { DragulaService } from "../../../../../../node_modules/ng2-dragula/ng2-dragula";
import { Team } from "../../../../shared/models/team";
import { Match } from "../../../../shared/models/match";
import { MatchDetails } from "../../../../shared/models/match-details";


@Component({
  selector: 'my-simulation-edit',
  templateUrl: './simulation-edit.component.html',
  styleUrls: ['./simulation-edit.component.css'],
  viewProviders: [DragulaService]
})

export class SimulationEditComponent implements OnInit {
  groupsPlan: MatchsPlan[] = [];
  pitchesPlan: PitchPlan[] = [];
  hours: Date [] = [];

  constructor(private simulDataService: SimulDataService,
              private dragulaService: DragulaService) {

    this.dragulaService.setOptions('match-bag', {
      invalid: (el, handle) => el.classList.contains('donotdrag'),
      //copy: true
    });
  }

  ngOnInit() {
    this.simulDataService.currentGroupsPlanSource.subscribe ( groupsPlan => {
      this.groupsPlan = groupsPlan;

      // get the number of time's matchs
      // consider the pitch with the most number of match in its planning
      let greatherPitchName: string = ''; // = this.groupsPlan[0].nameBlock;
      let greatherPitchLength: number = 0;

      // Get all match to made an drag n'drop
      let i = 0;
      this.groupsPlan.map( g => {
        if(g.planning.length > greatherPitchLength) { greatherPitchName =  g.nameBlock}

        this.pitchesPlan.push(new PitchPlan());
        g.planning.map ( m => {
          let listMatch = new ListMatch();
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

      // for pitchs fot those are less matchs in planning
      // Add some empty matchs
      this.pitchesPlan.map(p => {
          while(p.planning.length < this.hours.length){
            let listMatch = new ListMatch();
            p.planning.push(listMatch);
          }
      });
    });

    
    this.dragulaService.drag.subscribe((value:any) => {
      //console.log(`drag: ${value[0]}`); // value[0] will always be bag name
      this.onDrag(value.slice(1));
      this.checkConflict();
    });

    this.dragulaService.drop.subscribe((value:any) => {
      // console.log(`drop: ${value[0]}`);
      this.onDrop(value.slice(1));
      this.checkConflict();
    });

    this.dragulaService.over.subscribe((value:any) => {
      // console.log(`over: ${value[0]}`);
      this.onOver(value.slice(1));
      this.checkConflict();
    });
    
    this.dragulaService.out.subscribe((value:any) => {
      // console.log(`out: ${value[0]}`);
      this.onOut(value.slice(1));
      this.checkConflict();
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
   * Check if a team plays once per playtime 
   * Return false if no conflict is present
   */
  private checkConflict(){

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
          }
          if(!uniqueTeams.includes(m.equipe_visiteur)){
            uniqueTeams.push(m.equipe_visiteur);
          }
          else{
            m.isConflict = true;
          }

          // only if there is an self-referee
          /*
          if(m.equipe_arbitre){
            if(!uniqueTeams.includes(m.equipe_arbitre)){
             uniqueTeams.push(m.equipe_arbitre);
            }
            else{
              m.isConflict = true;
            } 
          }
          */         
        })
      }
    });
  }

}


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