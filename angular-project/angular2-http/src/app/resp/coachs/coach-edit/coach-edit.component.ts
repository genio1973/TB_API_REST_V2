import { Component, OnInit } from '@angular/core';
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { Coach } from "../../../shared/models/coach";
import { ActivatedRoute, Router } from "@angular/router";

@Component({
  selector: 'app-coach-edit',
templateUrl: './coach-edit.component.html',
  styleUrls: ['./coach-edit.component.css']
})
export class CoachEditComponent implements OnInit {

    coach:Coach;
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private service: RespTournamentService,
                private route: ActivatedRoute,
                private router: Router) { }

    ngOnInit() {
      // get the id from the url
      let id = this.route.snapshot.params['id'];

      this.service
          .getCoachs()
          .subscribe(coachs => {
            this.coach = coachs.find( c => c.id_personne == id);
          });

    }

    /**
     * Delete a coach
     */
        deleteCoach(){
        this.errorMessage = '';
        this.successMessage = '';

        this.service.deleteCoach(this.coach.id_personne)
          .subscribe(
            data => {
              this.successMessage = 'Equipe supprimée.';
              this.router.navigate(['/responsible/coachs']);
            } ,
            err => {
              this.errorMessage = err;
            });
    }

    /**
     * Update a coach
     */
        updateCoach(){
        this.errorMessage = '';
        this.successMessage = '';
        this.service.updateCoach(this.coach)
          .subscribe(
            coach => {
              this.successMessage = 'Coach est mis à jour.';
              this.router.navigate(['/responsible/coachs']);
            },
            err => {
              this.errorMessage = err;
            });
    }


}
