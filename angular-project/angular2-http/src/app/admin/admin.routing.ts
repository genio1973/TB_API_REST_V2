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
        ]
    },
];

export const adminRouting: ModuleWithProviders = RouterModule.forChild(adminRoutes);
