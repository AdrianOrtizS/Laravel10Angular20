import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { AuthService } from '../services/auth.service';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class WidgetsService {

  constructor(
    public http: HttpClient,
    public authService: AuthService
  ) { 
    
  }

  reportsSalesMonthly(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/reports/sales/monthly';
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  reportsSalesDaily(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/reports/sales/daily';
    return this.http.get(URL, {headers: headers})
            .pipe();
  }
 
  reportsProductsTop(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/reports/products/top';
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  reportsLowStock(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/reports/inventory/low_stock';
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

}
