import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { AuthService } from '../../services/auth.service';
import { URL_SERVICIOS } from '../../../config/config';
// import { URL_SERVICIOS } from 'src/app/config/config';
// import { AuthService } from '../services/auth.service';
// import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class ProductService {

  constructor(
    public http: HttpClient,
    public authService: AuthService
  ) { }

  listProducts( page:number = 1, search:string, id_categorie:number, pageSize:number ){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/products?page='+page+'&search='+search+'&id_categorie='+id_categorie+'&pageSize='+pageSize;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  getCategories(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/getCategories';
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createProduct(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/products';
    return this.http.post(URL, data, {headers: headers})
            .pipe();
  }

  showProduct(product_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/products/'+product_id;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  updateProduct(product_id:string, data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/products/'+product_id;
    return this.http.post(URL, data, {headers: headers})
            .pipe();
  }

  changeState(product_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/products/'+product_id;
    return this.http.delete(URL, {headers: headers})
            .pipe();
  }

}
