import { Component, Input, OnInit, OnDestroy } from '@angular/core';
import { Person } from '../person';
import { ActivatedRoute, Router } from "@angular/router";
import { PeopleService } from "../people.service";

@Component({
  selector: 'app-person-details',
  templateUrl: './person-details.component.html',
  styleUrls: ['./person-details.component.css']
})

export class PersonDetailsComponent implements OnInit, OnDestroy {
  /*
  // update PersonDetailsComponent
  // to decorate the person property with @Input()
  @Input() person : Person;
  */
  person: Person;
  sub: any;
  //professions: string[] = ['jedi', 'bounty hunter', 'princess', 'sith lord'];


  constructor(private peopleService: PeopleService,
              private route: ActivatedRoute,
              private router: Router){}


  ngOnInit(): void {
    this.sub = this.route.params.subscribe(params => {
      let id = Number.parseInt(params['id']);
      this.peopleService
          .get(id)
          .subscribe(p => this.person = p);
    });
  } 

  ngOnDestroy(){
      this.sub.unsubscribe();
  }

  gotoPeoplesList(){
    let link = ['/persons'];
    this.router.navigate(link);
  }

  savePersonDetails(){
    this.peopleService
        .save(this.person)
        .subscribe(r => console.log(`saved!!! ${JSON.stringify(this.person)}`));
        //alert(`saved!!! ${JSON.stringify(this.person)}`);
    }
}
