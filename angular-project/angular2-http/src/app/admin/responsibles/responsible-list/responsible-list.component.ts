import { Component, OnInit } from '@angular/core';
import { Responsible } from "../../../shared/models/responsible";
import { ResponsibleService } from "../../../shared/services/responsible.service";

@Component({
  selector: 'my-responsible-list',
  templateUrl: `./responsible-list.component.html`,
  styleUrls: ['./responsible-list.component.css']
})
export class ResponsibleListComponent implements OnInit {

    responsibles: Responsible[];  

    constructor( private responsibleService: ResponsibleService ){}

    ngOnInit(): void {
      console.log('ResponsibleList OnInit() !');
      this.responsibleService.getResponsibles()
          .subscribe(responsibles => this.responsibles = responsibles);
    }
}
