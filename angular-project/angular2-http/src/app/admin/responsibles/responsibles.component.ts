import { Component, OnInit } from '@angular/core';
import { AdminService } from "../../shared/services/admin.service";

@Component({
  selector: 'my-responsibles',
  templateUrl: `./responsibles.component.html`,
  styleUrls: ['./responsibles.component.css']
})

export class ResponsiblesComponent implements OnInit {
    successMessage: string = '';
    errorMessage: string = '';


    constructor( private adminService: AdminService ){}

    ngOnInit(): void {

      this.adminService.responsibleCreated$.subscribe( resp => {
            this.successMessage = `${resp.email} has been created with id ${resp.id} !`;
            this.clearMessages();
          });
      
      this.adminService.responsibleDeleted$.subscribe( res => {
            this.successMessage = `The user has been deleted !`;
            this.clearMessages();
          });

      this.adminService.responsibleUpdated$.subscribe( data => {
            this.successMessage = `The user has been updated !`;
            this.clearMessages();
          });  
 
      this.adminService.responsibleError$.subscribe( err => {
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
