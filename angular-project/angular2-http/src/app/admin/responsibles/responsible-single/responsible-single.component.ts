import { Component, OnInit } from '@angular/core';
import { Responsible } from "../../../shared/models/responsible";
import { AdminService } from "../../../shared/services/admin.service";
import { Router, ActivatedRoute } from "@angular/router";

@Component({
  selector: 'app-responsible-single',
  templateUrl: `./responsible-single.component.html`,
  styleUrls: ['./responsible-single.component.css']
})
export class ResponsibleSingleComponent implements OnInit {

    responsible: Responsible;
    errorMessage = '';
    successMessage = '';

    constructor(private adminService:AdminService,
                private router: Router,
                private route: ActivatedRoute) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.adminService
        .getResponsible(id)
        .subscribe(responsible => this.responsible = responsible);
    }

    deleteResponsible(){
        this.errorMessage = '';
        this.successMessage = '';
        this.adminService.deleteResponsible(this.responsible.id)
          .subscribe(
            data => {
              this.successMessage = 'User was deleted.';
              this.router.navigate(['/admin/resp']);
              //console.log('user was deleted');
            } ,
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }

}
