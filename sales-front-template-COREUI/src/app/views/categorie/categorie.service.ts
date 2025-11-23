import { AuthService } from '../services/auth.service';
import { URL_SERVICIOS } from '../../config/config';
import { Injectable, signal  } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';

@Injectable({
  providedIn: 'root'
})
export class CategorieService {

  constructor(public http: HttpClient,
    public authService: AuthService
  ) { }

  listCategories(page:number=1, search:string, pageSize:number){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/categories?page='+page+'&search='+search+'&pageSize='+pageSize;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createCategorie(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/categories';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showCategorie(categorie_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/categories/'+categorie_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  updateCategorie(categorie_id:string, data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/categories/'+categorie_id;
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  changeState(categorie_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/categories/'+categorie_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

}
