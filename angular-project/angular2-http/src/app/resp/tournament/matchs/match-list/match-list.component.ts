import { Component, OnInit, Input, OnChanges, SimpleChanges } from '@angular/core';
import { ConfigSimul } from "../../../../shared/plannings/ConfigSimul";
import { MatchsPlan } from "../../../../shared/plannings/matchs-plan";
import { Match } from "../../../../shared/models/match";
import { MatchDetails } from "../../../../shared/models/match-details";

@Component({
  selector: 'my-match-list',
  templateUrl: './match-list.component.html',
  styleUrls: ['./match-list.component.css']
})
export class MatchListComponent implements OnChanges {
  @Input() configSimul: ConfigSimul;
  @Input() groupsPlan: MatchsPlan[];
  matchs: MatchDetails[] = [];
  pitchesGroups: MatchDetails[] = [];

  constructor() { }

  ngOnChanges(changes: SimpleChanges): void {
    this.matchs = [];

    this.groupsPlan.map(g =>  this.matchs = this.matchs.concat(g.planning));

  }


}
