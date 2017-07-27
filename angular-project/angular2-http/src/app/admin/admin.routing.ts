import { ModuleWithProviders } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { AuthGuardService } from "../shared/guards/auth-guard.service";
import { AdminComponent } from "./admin.component";
import { AuthAdminGuardService } from "../shared/guards/auth-admin-guard.service";
import { ResponsiblesComponent } from "./responsibles/responsibles.component";
import { ResponsibleListComponent } from "./responsibles/responsible-list/responsible-list.component";
import { ResponsibleCreateComponent } from "./responsibles/responsible-create/responsible-create.component";
import { ResponsibleSingleComponent } from "./responsibles/responsible-single/responsible-single.component";
import { ResponsibleEditComponent } from "./responsibles/responsible-edit/responsible-edit.component";
import { UsersComponent } from "./users/users.component";
import { UserListComponent } from "./users/user-list/user-list.component";
import { UserCreateComponent } from "./users/user-create/user-create.component";
import { UserSingleComponent } from "./users/user-single/user-single.component";
import { UserEditComponent } from "./users/user-edit/user-edit.component";

export const adminRoutes: Routes = [
    { 
        path: 'admin',
        component: AdminComponent,
        children: [
        { path: '', redirectTo: 'resp', pathMatch: 'full' },
        //{ path: '', component: AdminComponent, canActivate: [AuthAdminGuardService]  },
        { 
            path: 'resp',
            component: ResponsiblesComponent,
            canActivateChild: [AuthAdminGuardService],
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
            canActivateChild: [AuthAdminGuardService],
            children: [
                        { path: '', redirectTo: 'list', pathMatch: 'full' },
                        { path: 'list',     component: UserListComponent },
                        { path: 'create',   component: UserCreateComponent },
                        { path: ':id',      component: UserSingleComponent },
                        { path: ':id/edit', component: UserEditComponent }
                    ]
        },
        ]
    },
];

export const adminRouting: ModuleWithProviders = RouterModule.forChild(adminRoutes);
