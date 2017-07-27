import { Component, OnInit } from '@angular/core';
import { PublicTournamentService } from "../../shared/services/public-tournament.service";

@Component({
  selector: 'my-tournaments',
  templateUrl: './tournaments.component.html',
  styleUrls: ['./tournaments.component.css']
})
export class TournamentsComponent implements OnInit {
    successMessage: string = '';
    errorMessage: string = '';


    constructor( private tournamentService: PublicTournamentService ){}

    ngOnInit(): void {
      this.tournamentService.tournament$.subscribe( errMsg => {
            this.errorMessage = errMsg + '. Est-ce le bon numéro de tournoi ?';
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
