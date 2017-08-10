import { Component, OnInit } from '@angular/core';
import { SimulDataService } from "../../../../shared/services/simul-data.service";

@Component({
  selector: 'app-simulation-edit',
  templateUrl: './simulation-edit.component.html',
  styleUrls: ['./simulation-edit.component.css']
})
export class SimulationEditComponent implements OnInit {

  message: string;

  constructor(private simulDataService: SimulDataService) { }

  ngOnInit() {
    this.simulDataService.currentMessage.subscribe( message => this.message = message );
  }

}
