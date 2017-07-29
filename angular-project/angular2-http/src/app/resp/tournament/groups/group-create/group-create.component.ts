import { Component, OnInit } from '@angular/core';
import { RespTournamentService } from "../../../../shared/services/resp.tournament.service";
import { Group } from "../../../../shared/models/group";
import { Router, ActivatedRoute } from "@angular/router";

@Component({
  selector: 'my-group-create',
  templateUrl: './group-create.component.html',
  styleUrls: ['./group-create.component.css']
})

export class GroupCreateComponent implements OnInit {

    groups: Group[] =  [{ id_groupe: null, nom_groupe:'', id_tournoi: 0}];
    tournamentId: number;
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private service: RespTournamentService,
                private router: Router,
                 private route: ActivatedRoute) { }

    ngOnInit() {
      this.groups[0] = new Group();
      
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
       this.groups[0].id_tournoi = params['idtournoi'];
      });
    }

    createGroup(){
        this.errorMessage = '';
        this.successMessage = '';

        this.service.createGroups(this.groups)
          .subscribe(
            user => {
              this.successMessage = 'Tournoi a été créé.';
              //this.router.navigate(['/admin/users/list']);
              //console.log('user was created');
            },
            err => {
              this.errorMessage = err;
              //console.log(err);
            });
    }

}
