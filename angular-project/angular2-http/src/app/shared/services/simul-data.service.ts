import { Injectable } from '@angular/core';
import { BehaviorSubject } from "rxjs/BehaviorSubject";
import { MatchsPlan } from "../plannings/matchs-plan";
import { MatchsGroupBy } from "../plannings/matchs-group-by";

@Injectable()
export class SimulDataService {


  //private groupsPlan: MatchsPlan[] = [];
  private groupsPlan: MatchsGroupBy[] = [];
  
  //private groupsPlanSource = new BehaviorSubject<MatchsPlan[]>(this.groupsPlan);
  private groupsPlanSource = new BehaviorSubject<MatchsGroupBy[]>(this.groupsPlan);
  currentGroupsPlanSource = this.groupsPlanSource.asObservable();

  constructor() { }

  //changeGroupsPlan(groupsPlan: MatchsPlan[]){
  changeGroupsPlan(groupsPlan: MatchsGroupBy[]){
    this.groupsPlanSource.next(groupsPlan);
  }
}