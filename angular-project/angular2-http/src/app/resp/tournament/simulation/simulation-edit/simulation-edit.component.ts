import { Component, OnInit, ViewChild } from '@angular/core';
import { SimulDataService } from "../../../../shared/services/simul-data.service";
import { MatchsPlan } from "../../../../shared/plannings/matchs-plan";
//import { DragulaService } from "ng2-dragula";
//import { DragulaService } from 'ng2-dragula/components/dragula.provider';
import { DragulaService } from "../../../../../../node_modules/ng2-dragula/ng2-dragula";
import { Team } from "../../../../shared/models/team";
import { Match } from "../../../../shared/models/match";
import { MatchDetails } from "../../../../shared/models/match-details";
//import { DragulaModule } from 'ng2-dragula/ng2-dragula';
//import { DragulaService } from 'ng2-dragula/components/dragula.provider';


@Component({
  selector: 'my-simulation-edit',
  templateUrl: './simulation-edit.component.html',
  styleUrls: ['./simulation-edit.component.css'],
  viewProviders: [DragulaService]
})

export class SimulationEditComponent implements OnInit {
  groupsPlan: MatchsPlan[] = [];
  pitchesPlan: PitchPlan[] = [];


  /*
    Rappel du type MatchPlan :  
      - planning: MatchDetails[] = [];
      - groupId: number;
      - nameBlock?: string = '';
      - private auto_arbitrage:boolean = false;

    Rappel de pitchesPlanSimul: 
      - planning: Planning[] = [];
      - namePitch?: string = '';


    Rappel RefereePlanSimul :
      - planning: Planning[] = [];
      - namePitch?: string = '';

    Rappel du type Planning : 
        matchs: MatchDetails[] = [];
    }
  */

  @ViewChild('match-bag') bag1: any;
  @ViewChild('referee-bag') bag2: any;

  constructor(private simulDataService: SimulDataService,
              private dragulaService: DragulaService) {
/*
              this.dragula.setOptions('bag-items', {
                revertOnSpill: true
              });
*/
     this.dragulaService.setOptions('match-bag', {
       invalid: (el, handle) => el.classList.contains('donotdrag')
     });

  }

  hours: Date [] = [];

  ngOnInit() {
    this.simulDataService.currentGroupsPlanSource.subscribe ( groupsPlan => {
          this.groupsPlan = groupsPlan;
          console.log(this.groupsPlan);

          // get the number of time's matchs
          // consider the pitch with the most number of match in its planning
          let greatherPitchName: string = this.groupsPlan[0].nameBlock;
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
            .planning.map( p => this.hours.push(p.date_match));

          // for pitchs fot those are less matchs in planning
          // Add some empty matchs
          let emptyTeam : Team = {nom_equipe:'Empty', id_equipe:0, id_groupe:0 };
          let m: MatchDetails = { equipe_home: emptyTeam, equipe_visiteur: emptyTeam, equipe_arbitre: emptyTeam };

          this.pitchesPlan.map(p => {
              while(p.planning.length < this.hours.length){
                let listMatch = new ListMatch();
                listMatch.matchs.push(m);
                p.planning.push(listMatch);
              }
          });

          console.log(this.pitchesPlan);
    });

    
    this.dragulaService.drag.subscribe((value:any) => {
      //console.log(`drag: ${value[0]}`); // value[0] will always be bag name
      this.onDrag(value.slice(1));
      // https://github.com/valor-software/ng2-dragula/issues/306
      // https://plnkr.co/edit/S55TLdOcq5Z0YQRGiMxo?p=preview
    });

    this.dragulaService.drop.subscribe((value:any) => {
      // console.log(`drop: ${value[0]}`);
      this.onDrop(value.slice(1));
    });

    this.dragulaService.over.subscribe((value:any) => {
      // console.log(`over: ${value[0]}`);
      this.onOver(value.slice(1));
    });
    
    this.dragulaService.out.subscribe((value:any) => {
      // console.log(`out: ${value[0]}`);
      this.onOut(value.slice(1));
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
}





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