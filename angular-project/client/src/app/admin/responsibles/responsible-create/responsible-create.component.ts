import { Component, OnInit } from '@angular/core';
import { Responsible } from "../../../shared/models/responsible";
import { Router } from "@angular/router";
import { AdminService } from "../../../shared/services/admin.service";

@Component({
  selector: 'my-responsible-create',
  templateUrl: `./responsible-create.component.html`,
  styleUrls: ['./responsible-create.component.css']
})
export class ResponsibleCreateComponent implements OnInit {

    responsible: Responsible; // =  { name:'', username:'',  avatar:''};
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private adminService: AdminService,
                private router: Router) { }

    ngOnInit() {
      this.responsible = new Responsible();
    }

    createResponsible(){
        this.errorMessage = '';
        this.successMessage = '';
        this.adminService.createResponsible(this.responsible)
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
