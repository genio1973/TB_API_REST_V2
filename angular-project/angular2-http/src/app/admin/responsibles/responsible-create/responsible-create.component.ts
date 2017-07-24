import { Component, OnInit } from '@angular/core';
import { Responsible } from "../../../shared/models/responsible";
import { Router } from "@angular/router";
import { ResponsibleService } from "../../../shared/services/responsible.service";

@Component({
  selector: 'my-responsible-create',
  templateUrl: `./responsible-create.component.html`,
  styleUrls: ['./responsible-create.component.css']
})
export class ResponsibleCreateComponent implements OnInit {

    responsible: Responsible; // =  { name:'', username:'',  avatar:''};
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private responsibleService: ResponsibleService,
                private router: Router) { }

    ngOnInit() {
      this.responsible = new Responsible();
    }

    createUser(){
        this.errorMessage = '';
        this.successMessage = '';
        this.responsibleService.createResponsible(this.responsible)
          .subscribe(
            responsible => {
              this.successMessage = 'User was created.';
              this.router.navigate(['/admin/resp']);
              //console.log('user was created');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }

}
