import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuardService } from "../shared/guards/auth-guard.service";
import { RespComponent } from "./resp.component";
import { TournamentListComponent } from "../public/tournaments/tournament-list/tournament-list.component";


export const respRoutes: Routes = [
    { 
        path: 'responsible',
        component: RespComponent,
        children: [
                    { path: '', redirectTo: '/account', pathMatch: 'full' },
                    { 
                        path: 'tournaments',
                        component: TournamentListComponent,
                        canActivateChild: [AuthGuardService]
            /*            ,
                        children: [
                                    { path: '', redirectTo: 'list', pathMatch: 'full' },
                                    { path: 'list',     component: ResponsibleListComponent },
                                    { path: 'create',   component: ResponsibleCreateComponent },
                                    { path: ':id',      component: ResponsibleSingleComponent },
                                    { path: ':id/edit', component: ResponsibleEditComponent }
                                ]
                                */
                    },            
                    
                   ]
    },
];

export const respRouting: ModuleWithProviders = RouterModule.forChild(respRoutes);
