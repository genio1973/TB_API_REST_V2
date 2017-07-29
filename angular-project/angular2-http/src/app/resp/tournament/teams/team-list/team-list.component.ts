import { Component, OnInit, Input } from '@angular/core';
import { Group } from "../../../../shared/models/group";
import { ActivatedRoute, Router } from "@angular/router";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { Team } from "../../../../shared/models/team";

@Component({
  selector: 'my-team-list',
  templateUrl: './team-list.component.html',
  styleUrls: ['./team-list.component.css']
})

export class TeamListComponent implements OnInit {
    @Input() group: Group;
    @Input('tournamentId') tournamentId: number;

    teams: Team[];  

    constructor( private service: PublicTournamentService,
                 private router: Router,
                 private route: ActivatedRoute ){}

    ngOnInit(): void {
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
        //this.tournamentId = params['idtournoi'];
      });

      this.service
          .getTeamsGroup(this.group.id_groupe)
          .subscribe(teams => 
            this.teams = teams
           
          );
    }
}