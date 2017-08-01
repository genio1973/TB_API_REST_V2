import { Component, OnInit } from '@angular/core';
import { Team } from "../../../shared/models/team";
import { PublicTournamentService } from "../../../shared/services/public-tournament.service";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { Coach } from "../../../shared/models/coach";
import { ActivatedRoute } from "@angular/router";

@Component({
  selector: 'app-coach-list',
  templateUrl: './coach-list.component.html',
  styleUrls: ['./coach-list.component.css']
})
export class CoachListComponent implements OnInit {
    errorMessage = '';
    successMessage = '';
    coachs: Coach[] = [];
    tournamentId: number;

    constructor( private RespService: RespTournamentService,
                 private service: PublicTournamentService,
                 private route: ActivatedRoute ){}

    ngOnInit(): void {
 
      // get the id from the url
      this.route.pathFromRoot[2].params.subscribe(params => {
        this.tournamentId = params['idtournoi'];
      });

      if(this.tournamentId){
        this.RespService
            .getTournamentTeams(this.tournamentId)
            .subscribe( teams => {
              teams.forEach(t => {
                this.coachs.push({ id_personne: t.id_personne, nom: t.nom, prenom: t.prenom,
                                    courriel: t.courriel, tel: t.tel, tel_mobile: t.tel_mobile,
                                    adresse: t.adresse, localite: t.localite});
              });
            });
      }
      else {
        this.RespService
            .getCoachs()
            .subscribe(coachs => this.coachs = coachs,
              err => {
                this.errorMessage = `Pas d'équipes à récupérer ou alors... ${err}`;
              });
      }
    }
          
}