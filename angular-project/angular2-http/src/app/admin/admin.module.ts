import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { FormsModule } from "@angular/forms";
import { AdminComponent } from "./admin.component";
import { ResponsiblesComponent } from "./responsibles/responsibles.component";
import { adminRouting } from "./admin.routing";
import { ResponsibleEditComponent } from "./responsibles/responsible-edit/responsible-edit.component";
import { ResponsibleSingleComponent } from "./responsibles/responsible-single/responsible-single.component";
import { ResponsibleCreateComponent } from "./responsibles/responsible-create/responsible-create.component";
import { ResponsibleListComponent } from "./responsibles/responsible-list/responsible-list.component";

@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    adminRouting,   
  ],
  declarations: [
    AdminComponent,
    ResponsiblesComponent,
    ResponsibleListComponent,
    ResponsibleCreateComponent,
    ResponsibleSingleComponent,
    ResponsibleEditComponent

  ]
})
export class AdminModule {}

