import { Component, OnInit } from '@angular/core';
import { SimulDataService } from "../../../../shared/services/simul-data.service";
import { MatchsPlan } from "../../../../shared/plannings/matchs-plan";

@Component({
  selector: 'app-simulation-edit',
  templateUrl: './simulation-edit.component.html',
  styleUrls: ['./simulation-edit.component.css']
})
export class SimulationEditComponent implements OnInit {

  groupsPlan: MatchsPlan[] = [];
  /*
    Rappel du type MatchPlan :  
      - planning: MatchDetails[] = [];
      - groupId: number;
      - nameBlock?: string = '';
      - private auto_arbitrage:boolean = false;
  */

  constructor(private simulDataService: SimulDataService) { }

  ngOnInit() {
    this.simulDataService.currentGroupsPlanSource.subscribe ( groupsPlan => this.groupsPlan = groupsPlan);
  }

}
