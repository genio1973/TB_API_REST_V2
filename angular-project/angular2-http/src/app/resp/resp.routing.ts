import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuardService } from "../shared/guards/auth-guard.service";
import { RespComponent } from "./resp.component";
import { TournamentsComponent } from "./tournaments/tournaments.component";
import { TournamentSingleComponent } from "./tournaments/tournament-single/tournament-single.component";
import { TournamentListComponent } from "./tournaments/tournament-list/tournament-list.component";
import { TournamentCreateComponent } from "./tournaments/tournament-create/tournament-create.component";
import { TournamentEditComponent } from "./tournaments/tournament-edit/tournament-edit.component";


export const respRoutes: Routes = [
    { 
        path: 'responsible',
        component: RespComponent,
        children: [
                    { path: '', redirectTo: '/account', pathMatch: 'full' },
                    { 
                        path: 'tournaments',
                        component: TournamentsComponent,
                        canActivateChild: [AuthGuardService]
                        ,
                        children: [
                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                    { path: 'list',     component: TournamentListComponent },
                                    { path: 'create',   component: TournamentCreateComponent },
                                    { path: ':id',      component: TournamentSingleComponent },
                                    { path: ':id/edit', component: TournamentEditComponent }
                                ]
                                
                    },            
                    
                   ]
    },
];

export const respRouting: ModuleWithProviders = RouterModule.forChild(respRoutes);
