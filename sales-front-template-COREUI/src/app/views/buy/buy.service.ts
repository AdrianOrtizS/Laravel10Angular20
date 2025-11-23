import { AuthService } from './../services/auth.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class BuyService {

  constructor(
    public http: HttpClient,
    public authService: AuthService
  ) { 
    
  }

  listBuys(page:number=1, search:string, pageSize:number, fecha_ini?:any, fecha_fin?:any, type_pay?:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/buys?page='+page+'&search='+search+
                                               '&pageSize='+pageSize+
                                               '&fecha_ini='+fecha_ini+
                                               '&fecha_fin='+fecha_fin+
                                               '&type_pay='+type_pay;
    
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createBuy(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/buys';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showBuy(buy_id:string|null){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/buys/'+buy_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  changeState(buy_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/buys/'+buy_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

  removeBuy(id_buy:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/buys/'+id_buy;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

  deletePay(id_buy:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/pays/'+id_buy;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

  getSuppliers(searchSupplier:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/buy/getSuppliers?searchSupplier='+searchSupplier;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  getProducts(searchProduct:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/buy/getProducts?searchProduct='+searchProduct;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  getConfigurations(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/configurations';
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  createPago(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/pays';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

}
