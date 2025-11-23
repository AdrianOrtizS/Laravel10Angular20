import { AuthService } from './../services/auth.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class PointsOfSaleService {

    constructor(
      public http: HttpClient,
      public authService: AuthService
    ) { }

    listPointsOfSale( page:number = 1, search:string, id_branch:number, pageSize:number ){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/pointsOfSale?page='+page+ '&search='+search+
                                                          '&id_branch='+id_branch+
                                                          '&pageSize='+pageSize;
      return this.http.get(URL, {headers: headers})
              .pipe();
    }

    getBranches(){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/getBranches';
      return this.http.get(URL, {headers: headers})
              .pipe();
    }

    getPointsByBranch(id_branch:any){
      let headers = new HttpHeaders();
      let URL = URL_SERVICIOS+'/getPointsByBranch/'+id_branch;
      return this.http.get(URL, {headers: headers})
              .pipe();
    }

    createPointsOfSael(data:any){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/pointsOfSale';
      return this.http.post(URL, data, {headers: headers})
              .pipe();
    }

    showPointsOfSale(pointOfSale_id:string){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/pointsOfSale/'+pointOfSale_id;
      return this.http.get(URL, {headers: headers})
              .pipe();
    }

    updatePointsOfSale(pointOfSale_id:string, data:any){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/pointsOfSale/'+pointOfSale_id;
      return this.http.put(URL, data, {headers: headers})
              .pipe();
    }

    changeState(pointOfSale_id:string){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/pointsOfSale/'+pointOfSale_id;
      return this.http.delete(URL, {headers: headers})
              .pipe();
    }

}
