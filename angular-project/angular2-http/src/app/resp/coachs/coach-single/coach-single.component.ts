import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from "@angular/router";
import { Coach } from "../../../shared/models/coach";
import { RespTournamentService } from "../../../shared/services/resp.tournament.service";

@Component({
  selector: 'app-coach-single',
  templateUrl: './coach-single.component.html',
  styleUrls: ['./coach-single.component.css']
})
export class CoachSingleComponent implements OnInit {
    coach: Coach;
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

}
