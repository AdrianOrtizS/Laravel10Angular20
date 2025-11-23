import { AuthService } from './../services/auth.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class SupplierService {

  constructor(public http: HttpClient,
    public authService: AuthService
  ) { }

  listSuppliers(page:number=1, search:string, pageSize:number){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/suppliers?page='+page+'&search='+search+'&pageSize='+pageSize;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createSupplier(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/suppliers';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showSupplier(supplier_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/suppliers/'+supplier_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  updateSupplier(supplier_id:string, data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/suppliers/'+supplier_id;
    return this.http.put(URL, data, {headers: headers})
    .pipe();
  }

  changeState(supplier_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/suppliers/'+supplier_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

}
