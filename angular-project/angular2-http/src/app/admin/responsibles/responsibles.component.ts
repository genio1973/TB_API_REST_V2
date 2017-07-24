import { Component, OnInit } from '@angular/core';
import { ResponsibleService } from "../../shared/services/responsible.service";

@Component({
  selector: 'my-responsibles',
  templateUrl: `./responsibles.component.html`,
  styleUrls: ['./responsibles.component.css']
})

export class ResponsiblesComponent implements OnInit {
    successMessage: string = 'vide';
    errorMessage: string = 'vide';


    constructor( private responsibleService: ResponsibleService ){}

    ngOnInit(): void {

      this.responsibleService.responsibleCreated$.subscribe( resp => {
            this.successMessage = `${resp.email} has been created with id ${resp.id} !`;
            this.clearMessages();
          });
      
      this.responsibleService.responsibleDeleted$.subscribe( res => {
            this.successMessage = `The user has been deleted !`;
            this.clearMessages();
          });

      this.responsibleService.responsibleUpdated$.subscribe( data => {
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
