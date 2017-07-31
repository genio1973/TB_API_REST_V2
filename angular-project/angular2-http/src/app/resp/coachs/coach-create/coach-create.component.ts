import { Component, OnInit } from '@angular/core';
import { Coach } from "../../../shared/models/coach";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";
import { Router } from "@angular/router";

@Component({
  selector: 'app-coach-create',
templateUrl: './coach-create.component.html',
  styleUrls: ['./coach-create.component.css']
})
export class CoachCreateComponent implements OnInit {
    coachs: Coach[] = [{prenom:'', nom:'', courriel:''}];
    successMessage: string = '';
    errorMessage: string = '';

    constructor(private service: RespTournamentService,
                private router: Router) { }

    ngOnInit() {
    }

    /**
     * Create a coach
     */
        createCoach(){
        this.errorMessage = '';
        this.successMessage = '';

        this.service.createCoachs(this.coachs)
          .subscribe(
            data => {
              this.successMessage = `Le coach a été créé.`;
              this.router.navigate(['/responsible/coachs']);
            },
            err => {
              this.errorMessage = err;
            });
    }
}
