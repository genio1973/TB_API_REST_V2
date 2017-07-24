import { Component, OnInit } from '@angular/core';
import { Responsible } from "../../../shared/models/responsible";
import { ResponsibleService } from "../../../shared/services/responsible.service";
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

    constructor(private responsibleService: ResponsibleService,
                private route: ActivatedRoute,
                private router: Router) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.responsibleService
          .getResponsible(id)
          .subscribe(responsible=> this.responsible = responsible);
    }

    updateResponsible(){
        this.errorMessage = '';
        this.successMessage = '';
        this.responsibleService.updateResponsible(this.responsible)
          .subscribe(
            user => {
              this.successMessage = 'User was updated.';
              this.router.navigate(['/admin/resp']);
              //console.log('user was updated');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }


}

