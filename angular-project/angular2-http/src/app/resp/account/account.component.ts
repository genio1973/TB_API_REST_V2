import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { Responsible } from "../../shared/models/responsible";
import { ResponsibleService } from "../../shared/services/responsible.service";

@Component({
  selector: 'my-account',
  templateUrl: `./account.component.html`,
  styleUrls: ['./account.component.css']
})


export class AccountComponent implements OnInit {
    responsible: Responsible;
    successMessage: string = '';
    errorMessage: string = '';
    needPasswordChange: boolean = false;
    email_confirm: string = '';
    mot_de_passe_confirm: string = '';
    needEmailConfirm: boolean = false;

    constructor(private responsibleService: ResponsibleService,
                private route: ActivatedRoute,
                private router: Router) { }

    ngOnInit() {
      this.responsibleService
          .getAccountInfo()
          .subscribe(responsible => this.responsible = responsible);
    }

    updateResponsible(){
        let userEdit;
        let userNoPwd : ResponsibleTemp;
        this.errorMessage = '';
        this.successMessage = '';
        
        if(this.email_confirm !=='' && this.responsible.email !== this.email_confirm) {
          this.errorMessage = "Les adresses ne sont pas identiques";
          return;
        }

        if(this.needPasswordChange &&  this.responsible.mot_de_passe !== this.mot_de_passe_confirm) {
          this.errorMessage = "Les mots de passe ne sont pas identiques";
          return;
        }

        if(!this.needPasswordChange){ userEdit = userNoPwd = this.responsible; }
        else { userEdit = this.responsible; }

        this.responsibleService.updatePersonnalAccount(userEdit)
          .subscribe(
            user => {
              this.successMessage = 'User was updated.';              
              this.router.navigate(['/account']);
              //console.log('user was updated');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
        this.clearMessages();
    }

    togglePasswordChangeButton(){
      this.needPasswordChange = !this.needPasswordChange;
    }

    showEmailConfirm(){
      this.needEmailConfirm = true;
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

class ResponsibleTemp{
    id: number;
    email: string;
    token_expire?: Date;
    nom_user: string;
    prenom_user: string;
    status: number;
    id_role: number;
    droits?: string;
}