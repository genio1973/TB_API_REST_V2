import { Component, OnInit, Input } from '@angular/core';
import { Resultat } from "../../../../shared/models/resultat";

@Component({
  selector: 'my-result-single',
  templateUrl:'./result-single.component.html',
  styleUrls: ['./result-single.component.css']
})
export class ResultSingleComponent implements OnInit {

  @Input() result: Resultat;
  @Input() displayType: string;
  
  constructor() { }

  ngOnInit() {
  }

}
