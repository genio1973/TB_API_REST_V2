import { Component, OnInit } from '@angular/core';
import { Responsible } from "../../../shared/models/responsible";
import { AdminService } from "../../../shared/services/admin.service";
import { ActivatedRoute, Router } from "@angular/router";

@Component({
  selector: 'my-responsible-edit',
  templateUrl: `./responsible-edit.component.html`,
  styleUrls: ['./responsible-edit.component.css']
})

export class ResponsibleEditComponent implements OnInit {
    responsible: Responsible;
    successMessage: string = '';
    errorMessage: string = '';
    needPasswordChange: boolean = false;

    constructor(private adminService: AdminService,
                private route: ActivatedRoute,
                private router: Router) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.adminService
          .getResponsible(id)
          .subscribe(responsible=> this.responsible = responsible);
    }

    updateResponsible(){
        let userEdit;
        let userNoPwd : ResponsibleTemp;
        this.errorMessage = '';
        this.successMessage = '';
        
        if(!this.needPasswordChange){ userEdit = userNoPwd = this.responsible; }
        else { userEdit = this.responsible; }

        this.adminService.updateResponsible(userEdit)
          .subscribe(
            user => {
              this.successMessage = 'User was updated.';
              this.router.navigate(['/admin/resp']);
            },
            err => {
              this.errorMessage = err;
            });
    }

    togglePasswordChangeButton(){
      this.needPasswordChange = !this.needPasswordChange;
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