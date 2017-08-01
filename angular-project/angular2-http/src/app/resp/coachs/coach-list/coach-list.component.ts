import { Component, OnInit } from '@angular/core';
import { Team } from "../../../shared/models/team";
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { Coach } from "../../../shared/models/coach";

@Component({
  selector: 'app-coach-list',
  templateUrl: './coach-list.component.html',
  styleUrls: ['./coach-list.component.css']
})
export class CoachListComponent implements OnInit {
    errorMessage = '';
    successMessage = '';
    coachs: Coach[];

    constructor( private service: RespTournamentService ){}

    ngOnInit(): void {

        this.service
            .getCoachs()
            .subscribe(coachs => {
                this.coachs = coachs;
              },
              err => {
                this.errorMessage = `Pas d'équipes à récupérer ou alors... ${err}`;
              });
    }
          
  /**
   * Clear all messages after 5 sec
   */
  clearMessages(){
    
    setTimeout(() => {
      this.errorMessage = '';
      this.successMessage = '';  
    }, 5000);
  }
}