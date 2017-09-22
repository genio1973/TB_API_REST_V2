import { Component, OnInit, Input } from '@angular/core';
import { MatchsPlan } from "../../../../../shared/plannings/matchs-plan";
import { Match } from "../../../../../shared/models/match";
import { MatchDetails } from "../../../../../shared/models/match-details";

@Component({
  selector: 'my-match-list',
  templateUrl: './match-list.component.html',
  styleUrls: ['./match-list.component.css']
})
export class MatchListComponent implements OnInit{
  //@Input() groupsPlan: MatchsPlan[];
  @Input() matchs: MatchDetails[] = [];
  
  constructor() { }

    ngOnInit(): void {
   
  }
}
