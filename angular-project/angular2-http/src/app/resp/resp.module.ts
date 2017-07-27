import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from "@angular/forms";
import { respRouting } from "./resp.routing";
import { RespComponent } from "./resp.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentSingleComponent } from "./tournaments/tournament-single/tournament-single.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { TournamentCreateComponent } from "./tournaments/tournament-create/tournament-create.component";


@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    respRouting,   
  ],
  declarations: [
    RespComponent,
    TournamentsComponent,
    TournamentSingleComponent,
    TournamentListComponent,
    TournamentCreateComponent,
  ],
  providers: [
  ]
})
export class RespModule {}

