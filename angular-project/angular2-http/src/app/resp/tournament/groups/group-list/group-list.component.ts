import { Component, OnInit } from '@angular/core';
import { Group } from "../../../../shared/models/group";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { Router, ActivatedRoute, Params } from "@angular/router";
import { Tournament } from "../../../../shared/models/tournament";


@Component({
  selector: 'my-group-list',
  templateUrl: './group-list.component.html',
  styleUrls: ['./group-list.component.css']
})
export class GroupListComponent implements OnInit {
    groups: Group[];  
    tournamentId: number;
    responsibleRoute:boolean = true;

    constructor( private service: PublicTournamentService,
                 private router: Router,
                 private route: ActivatedRoute ){}

    ngOnInit(): void {
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
        this.tournamentId = params['idtournoi'];
      });
      
      // Detect if its a public route or not
      if(this.route.pathFromRoot[1].snapshot.url[0].path === 'public'){
        this.responsibleRoute = false;
      }

      this.service
          .getGroupsTournament(this.tournamentId)
          .subscribe(groups => this.groups = groups);
    }

}
