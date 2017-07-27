import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { PublicComponent } from "./public.component";
import { LoginComponent } from "./login/login.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { TournamentSingleComponent } from "./tournaments/tournament-single/tournament-single.component";
import { NotFoundComponent } from "../not-found/not-found.component";

export const publicRoutes: Routes = [         
        { path: 'public',
          component: PublicComponent,
          children: [
              { path: '', redirectTo: 'tournaments', pathMatch: 'full' },
              { path: 'login', component: LoginComponent },
              {
                path: 'tournaments',
                component: TournamentsComponent,
                children:[
                            { path: '', redirectTo: 'list', pathMatch: 'full' },
                            { path: 'list',    component: TournamentListComponent },
                            { path: ':id',     component: TournamentSingleComponent },

                      ] }
              ]
        } 
    ];

export const publicRouting: ModuleWithProviders = RouterModule.forChild(publicRoutes);
