import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormsModule } from "@angular/forms";
import { AdminComponent } from "./admin.component";
import { UsersComponent } from "./users/users.component";
import { ResponsiblesComponent } from "./responsibles/responsibles.component";
import { adminRouting } from "./admin.routing";
import { UserListComponent } from "./users/user-list/user-list.component";
import { ResponsibleEditComponent } from "./responsibles/responsible-edit/responsible-edit.component";
import { ResponsibleSingleComponent } from "./responsibles/responsible-single/responsible-single.component";
import { ResponsibleCreateComponent } from "./responsibles/responsible-create/responsible-create.component";
import { ResponsibleListComponent } from "./responsibles/responsible-list/responsible-list.component";
import { UserEditComponent } from "./users/user-edit/user-edit.component";
import { UserSingleComponent } from "./users/user-single/user-single.component";
import { UserCreateComponent } from "./users/user-create/user-create.component";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    adminRouting,   
  ],
  declarations: [
    AdminComponent,
    UsersComponent,
    ResponsiblesComponent,

    UserListComponent,
    UserCreateComponent,
    UserSingleComponent,
    UserEditComponent,
    ResponsibleListComponent,
    ResponsibleCreateComponent,
    ResponsibleSingleComponent,
    ResponsibleEditComponent

  ]
})
export class AdminModule {}

