import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { publicRouting } from './public.routing';
import { PublicComponent } from "./public.component";
import { LoginComponent } from "./login/login.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { TournamentSingleComponent } from "./tournaments/tournament-single/tournament-single.component";
import { FormsModule } from "@angular/forms";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    publicRouting
  ],
  declarations: [
    PublicComponent,
    LoginComponent,
    TournamentsComponent,
    TournamentListComponent,
    TournamentSingleComponent
  ],
  providers: [

  ]
})
export class PublicModule {}
