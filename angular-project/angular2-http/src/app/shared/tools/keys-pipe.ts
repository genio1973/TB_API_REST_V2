// source : https://stackoverflow.com/questions/44429806/iterate-over-an-array-of-objects-and-get-the-key-angular-2

import { Pipe, PipeTransform } from '@angular/core';

@Pipe({name: 'keys'})
export class KeysPipe implements PipeTransform {
  transform(value, args:string[]) : any {
    let keys = [];
    Object.keys(value).forEach(function(key){
      keys.push({key: key, value: value['value']});
    }); 

    return keys;
  }
}
