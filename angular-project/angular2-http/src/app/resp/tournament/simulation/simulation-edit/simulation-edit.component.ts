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

 // @ViewChild('match-bag') bag1: any;
 // @ViewChild('referee-bag') bag2: any;

  constructor(private simulDataService: SimulDataService,
              private dragulaService: DragulaService) {

     this.dragulaService.setOptions('match-bag', {
       invalid: (el, handle) => el.classList.contains('donotdrag'),
       //copy: true
     });


  }

  hours: Date [] = [];

  ngOnInit() {
    this.simulDataService.currentGroupsPlanSource.subscribe ( groupsPlan => {
          this.groupsPlan = groupsPlan;
          //console.log(this.groupsPlan);

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
          //let emptyTeam : Team = {nom_equipe:'Empty', id_equipe:0, id_groupe:0 };
          //let m: MatchDetails = { equipe_home: emptyTeam, equipe_visiteur: emptyTeam, equipe_arbitre: emptyTeam };

          this.pitchesPlan.map(p => {
              while(p.planning.length < this.hours.length){
                let listMatch = new ListMatch();
                //listMatch.matchs.push(m);
                p.planning.push(listMatch);
              }
          });

          //console.log('Après remplissage avec de match vide');
          //console.log(this.pitchesPlan);
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
 /* 
   this.dragulaService.dropModel.subscribe((value:any) => {
      // console.log(`drop: ${value[0]}`);
      this.onDrop(value.slice(1));
    });
*/
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

    //this.rowSource = Number(e.dataset.indexrowhour);
    //this.colSource = Number(e.dataset.indexcolpitch);

  }

  //colSource:number;
  //rowSource:number;


  private onDrop(args:any):void {
    let [e] = args;
    this.addClass(e, 'ex-moved');

/*    
    let [bagName, el, target, source] = args;
        let transferData:any = {
        rowSource: Number(el.dataset.indexrowhour),
        colSource: Number(el.dataset.indexcolpitch)
     }

console.log('el:');
console.log(el); console.log(' ');
console.log('target:');
console.log(target); console.log(' ');
console.log('source:');
console.log(source); console.log(' ');
console.log(`Source: row : ${this.rowSource} and col: ${this.colSource}`);
//console.log(`Cible: row : ${this.rowSource} and col: ${this.colSource}`);
*/


    // Get source and target coordinate
 //   let [el, target, source, sibling] = args;
/*    
    console.log('el:');
    console.log(el); console.log(' ');
    console.log('target:');
    console.log(target); console.log(' ');
    console.log('source:');
    console.log(source); console.log(' ');
    console.log('sibling:');
    console.log(sibling); console.log(' ');
*/

/*
    // console.log(target.parentElement.children);

    // let transferData:any = {
    //     rowSource: Number(el.dataset.indexrowhour),
    //     colSource: Number(el.dataset.indexcolpitch)
    //  }

    // Already match in destination ?
    if(sibling){
      let transferData:any = {
          rowSource: Number(el.dataset.indexrowhour),
          colSource: Number(el.dataset.indexcolpitch),
          rowSibling: Number(sibling.dataset.indexrowhour),
          colSibling: Number(sibling.dataset.indexcolpitch),
          teamHomeSourceId: Number(el.dataset.teamhomeid),
          teamVisiteurSourceId: Number(el.dataset.teamvisiteurid),
          teamHomeTargetId: Number(sibling.dataset.teamhomeid),
          teamVisiteurTargetId: Number(sibling.dataset.teamvisiteurid),
      }
    

      console.log('transferData');
      console.log(transferData);


      // Tranfert the match in target to the source to make a swap
      // 1st : Finde and Get out the match in target by finding it in list with id_equipe (home & visiteur)
      // 2nb : Push it on the source matchs list
      

      // Get the previous target match
      let targetMatch: MatchDetails = this.pitchesPlan[transferData.colSibling]
                                          .planning[transferData.rowSibling]
                                          .matchs.find( m => m.equipe_home.id_equipe == transferData.teamHomeSourceId
                                                              && m.equipe_visiteur.id_equipe == transferData.teamVisiteurSourceId);
      
      // Delete the previous match from the target
      
      let indexMatch:number = this.pitchesPlan[transferData.colSibling]
                                          .planning[transferData.rowSibling]
                                          .matchs.indexOf(targetMatch);
      this.pitchesPlan[transferData.colSibling].planning[transferData.rowSibling].matchs.splice(indexMatch, 1);
    
      // push the match in source
      this.pitchesPlan[transferData.colSource].planning[transferData.rowSource].matchs.push(targetMatch);
    }
 */


  //    console.log('Après Déplacement');
  //    console.log(this.pitchesPlan);
  }

  private onOver(args:any):void {
    let [el] = args;
    this.addClass(el, 'ex-over');

    // let [e, target, source] = args;
    // console.log('onOver:');
    // console.log(e);
    // console.log(target);
    // console.log(source);
  }

  private onOut(args:any):void {
    let [el] = args;
    this.removeClass(el, 'ex-over');


    // let [e, target, source] = args;
    // console.log('onOut:');
    // console.log(e);
    // console.log(target);
    // console.log(source);
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