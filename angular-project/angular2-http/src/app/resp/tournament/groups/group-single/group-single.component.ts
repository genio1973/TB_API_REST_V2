import { Component, OnInit } from '@angular/core';
import { Group } from "../../../../shared/models/group";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { Router, ActivatedRoute } from "@angular/router";

@Component({
  selector: 'app-group-single',
  templateUrl: './group-single.component.html',
  styleUrls: ['./group-single.component.css']
})
export class GroupSingleComponent implements OnInit {
    group: Group = { id_groupe: null, nom_groupe:'', id_tournoi: 0};
    groups: Group[];
    id_groupe: number;
    tournamentId: number;
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private publicService: PublicTournamentService,
                private router: Router,
                private route: ActivatedRoute) {                   
                }

    ngOnInit() {
      
      let tournamentId = 0;
      
      // get the id's from the url
      this.id_groupe = this.route.snapshot.params['idgroup'];
      this.route.pathFromRoot[2].params.subscribe(params => {
        this.tournamentId = params['idtournoi'];
      });

      this.publicService
          .getGroupsTournament(this.tournamentId)
          .subscribe(gps =>{
            this.groups = gps;
            this.group = gps.find(g => g.id_groupe === this.id_groupe);
          } );
    }

}
