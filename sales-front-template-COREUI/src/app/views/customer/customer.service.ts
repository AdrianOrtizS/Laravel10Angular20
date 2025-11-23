import { AuthService } from './../services/auth.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class CustomerService {

  constructor(public http: HttpClient,
    public authService: AuthService
  ) { }

  listCustomers(page:number=1, search:string, pageSize:number){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/customers?page='+page+'&search='+search+'&pageSize='+pageSize;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createCustomer(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/customers';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showCustomer(customer_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/customers/'+customer_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  updateCustomer(customer_id:string, data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/customers/'+customer_id;
    return this.http.put(URL, data, {headers: headers})
    .pipe();
  }

  changeState(customer_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/customers/'+customer_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

}
