import { Component, OnInit } from '@angular/core';
import { Responsible } from "../../../shared/models/responsible";
import { AdminService } from "../../../shared/services/admin.service";

@Component({
  selector: 'my-responsible-list',
  templateUrl: `./responsible-list.component.html`,
  styleUrls: ['./responsible-list.component.css']
})
export class ResponsibleListComponent implements OnInit {

    responsibles: Responsible[];  

    constructor( private adminService: AdminService ){}

    ngOnInit(): void {
      console.log('ResponsibleList OnInit() !');
      this.adminService.getResponsibles()
          .subscribe(responsibles => this.responsibles = responsibles);
    }
}
