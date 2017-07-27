import { Component, OnInit } from '@angular/core';
import { RespTournamentService } from "../../shared/services/resp.tournament.service";

@Component({
  selector: 'app-tournaments',
  templateUrl: `./tournaments.component.html`,
  styleUrls: ['./tournaments.component.css']
})
export class TournamentsComponent implements OnInit {

    successMessage: string = '';
    errorMessage: string = '';


    constructor( private tournamentService: RespTournamentService ){}

    ngOnInit(): void {
      this.tournamentService.tournament$.subscribe( data => {
              this.successMessage = data;
              this.clearMessages();
            } ,
            err => {
              this.errorMessage = err;
              this.clearMessages();
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
