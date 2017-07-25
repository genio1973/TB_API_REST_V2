import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { UsersComponent } from "./admin/users/users.component";
import { UserListComponent } from "./admin/users/user-list/user-list.component";
import { UserCreateComponent } from "./admin/users/user-create/user-create.component";
import { UserSingleComponent } from "./admin/users/user-single/user-single.component";
import { UserEditComponent } from "./admin/users/user-edit/user-edit.component";
import { TournamentListComponent } from "./public/tournaments/tournament-list/tournament-list.component";
import { TournamentsComponent } from "./public/tournaments/tournaments.component";
import { TournamentSingleComponent } from "./public/tournaments/tournament-single/tournament-single.component";
import { ResponsibleListComponent } from "./admin/responsibles/responsible-list/responsible-list.component";
import { ResponsibleEditComponent } from "./admin/responsibles/responsible-edit/responsible-edit.component";
import { ResponsibleSingleComponent } from "./admin/responsibles/responsible-single/responsible-single.component";
import { ResponsibleCreateComponent } from "./admin/responsibles/responsible-create/responsible-create.component";
import { AdminComponent } from "./admin/admin.component";
import { ResponsiblesComponent } from "./admin/responsibles/responsibles.component";
import { PublicComponent } from "./public/public.component";

export const routes: Routes = [
        { path: '', redirectTo: 'public', pathMatch: 'full' },        
        { path: 'admin',
          component: AdminComponent,
          children: [
            { path: '', redirectTo: 'resp', pathMatch: 'full' },
            { 
              path: 'resp',
              component: ResponsiblesComponent,
              children: [
                            { path: '', redirectTo: 'list', pathMatch: 'full' },
                            { path: 'list',     component: ResponsibleListComponent },
                            { path: 'create',   component: ResponsibleCreateComponent },
                            { path: ':id',      component: ResponsibleSingleComponent },
                            { path: ':id/edit', component: ResponsibleEditComponent }
                        ]
            },
            { 
              path: 'users',
              component: UsersComponent,
              children: [
                            { path: '', redirectTo: 'list', pathMatch: 'full' },
                            { path: 'list',     component: UserListComponent },
                            { path: 'create',   component: UserCreateComponent },
                            { path: ':id',      component: UserSingleComponent },
                            { path: ':id/edit', component: UserEditComponent }
                        ]
            }
            ]
        },
        { path: 'public',
          component: PublicComponent,
          children: [
              { path: '', redirectTo: 'tournaments', pathMatch: 'full' },
              {
                path: 'tournaments',
                component: ResponsiblesComponent,
                children:[
                            { path: '', redirectTo: 'list', pathMatch: 'full' },
                            { path: 'list',    component: TournamentListComponent },
                            { path: ':id',     component: TournamentSingleComponent },

                      ] }
              ]
        }
    ];

export const routing: ModuleWithProviders = RouterModule.forRoot(routes);
