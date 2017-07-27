import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from "@angular/forms";
import { respRouting } from "./resp.routing";
import { RespComponent } from "./resp.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentSingleComponent } from "./tournaments/tournament-single/tournament-single.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { TournamentCreateComponent } from "./tournaments/tournament-create/tournament-create.component";
import { TournamentEditComponent } from "./tournaments/tournament-edit/tournament-edit.component";



@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    respRouting
  ],
  declarations: [
    RespComponent,
    TournamentsComponent,
    TournamentSingleComponent,
    TournamentListComponent,
    TournamentCreateComponent,
    TournamentEditComponent,
  ],
  providers: [
  ]
})
export class RespModule {}

