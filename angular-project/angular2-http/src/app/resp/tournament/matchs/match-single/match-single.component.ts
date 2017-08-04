import { Component, OnInit, Input } from '@angular/core';
import { Match } from "../../../../shared/models/match";

@Component({
  selector: 'my-match-single',
  templateUrl: 'match-single.component.html',
  styleUrls: ['./match-single.component.css']
})
export class MatchSingleComponent implements OnInit {
  @Input() match: Match;

  constructor() { }

  ngOnInit() {
    
  }

}
