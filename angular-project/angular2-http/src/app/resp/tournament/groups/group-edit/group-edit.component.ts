import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from "@angular/router";
import { Group } from "../../../../shared/models/group";
import { PublicTournamentService } from "../../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";

@Component({
  selector: 'my-group-edit',
  templateUrl: './group-edit.component.html',
  styleUrls: ['./group-edit.component.css']
})
export class GroupEditComponent implements OnInit {
    group: Group = { id_groupe: null, nom_groupe:'', id_tournoi: 0};
    groups: Group[];
    id_groupe: number;
    tournamentId: number;
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private publicService: PublicTournamentService,
                private respService: RespTournamentService,
                private router: Router,
                private route: ActivatedRoute) {                   
                }

    ngOnInit() {
      
      let tournamentId = 0;
      
      // get the id from the url
      this.id_groupe = this.route.snapshot.params['idgroup'];
      // get the id from the url
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

    updateGroup(){
        this.errorMessage = '';
        this.successMessage = '';
        console.log(this.group);

        this.respService.updateGroup(this.group)
          .subscribe(
            user => {
              this.successMessage = 'Tournoi a été mis à jour.';
              this.router.navigate(['/responsible/tournament',  this.tournamentId, 'groups']);
              //[routerLink]="['/responsible/tournament',  this.tournamentId, 'groups','create'] "
              //console.log('user was created');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }

}