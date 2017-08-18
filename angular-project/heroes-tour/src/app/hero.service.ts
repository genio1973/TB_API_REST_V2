import { Injectable }    from '@angular/core';
import { Headers, Http } from '@angular/http';

import 'rxjs/add/operator/toPromise';
import 'rxjs/add/operator/map';

import { Hero } from './hero';
import { Observable } from "rxjs/Observable";


@Injectable()
export class HeroService {
    //private heroesUrl = 'api/heroes';  // URL to web api
    //private heroesUrl = 'http://test.romandvolley.ch/api/v1/public/equipes/groupe/1';
    private heroesUrl = 'http://test.romandvolley.ch/api/v1/public';
    
    constructor(private http: Http) { }
    getHeroes(): Promise<Hero[]> {

        return this.http.get(this.heroesUrl)
                .toPromise()
                .then(response => response.json().data as Hero[])
                .catch(this.handleError);
    }

    searchHeroes(): Observable<Hero[]> {
        return this.http
                .get(this.heroesUrl + '/equipes/groupe/1')
                .map(response => response.json().data as Hero[]);
    }

  getHero(id: number): Promise<Hero> {
    const url = `${this.heroesUrl}/${id}`;
    return this.http.get(url)
      .toPromise()
      .then(response => response.json().data as Hero)
      .catch(this.handleError);
  }

    private handleError(error: any): Promise<any> {
    console.error('An error occurred', error); // for demo purposes only
    return Promise.reject(error.message || error);
    }

}
