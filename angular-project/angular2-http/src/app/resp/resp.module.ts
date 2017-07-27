import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from "@angular/forms";
import { respRouting } from "./resp.routing";
import { RespComponent } from "./resp.component";


@NgModule({
  imports: [
    CommonModule,
    FormsModule,
    respRouting,   
  ],
  declarations: [
    RespComponent,
  ],
  providers: [
  ]
})
export class RespModule {}

