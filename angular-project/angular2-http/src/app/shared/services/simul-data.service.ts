import { Injectable } from '@angular/core';
import { BehaviorSubject } from "rxjs/BehaviorSubject";
import { MatchsPlan } from "../plannings/matchs-plan";

@Injectable()
export class SimulDataService {


  private groupsPlan: MatchsPlan[] = [];
  private groupsPlanSource = new BehaviorSubject<MatchsPlan[]>(this.groupsPlan);
  currentGroupsPlanSource = this.groupsPlanSource.asObservable();

  constructor() { }

  changeGroupsPlan(groupsPlan: MatchsPlan[]){
    this.groupsPlanSource.next(groupsPlan);
    console.log(groupsPlan);

  }
}
