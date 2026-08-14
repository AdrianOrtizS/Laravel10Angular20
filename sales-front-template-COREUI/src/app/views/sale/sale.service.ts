import { AuthService } from './../services/auth.service';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class SaleService {

  constructor(
    public http: HttpClient,
    public authService: AuthService
  ) { 
    
  }

  listSales(page:number=1, search:string, pageSize:number, fecha_ini?:any, fecha_fin?:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sales?page='+page+
                                  '&search='+search+
                                  '&pageSize='+pageSize+
                                  '&fecha_ini='+fecha_ini+
                                  '&fecha_fin='+fecha_fin;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  listSalesExcel(fecha_ini?:any, fecha_fin?:any) {
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/excel';
    const params = new HttpParams()
        .set('fecha_ini', fecha_ini)
        .set('fecha_fin', fecha_fin);
    return this.http.get(URL, {headers, params, responseType:'blob'});
  }

  listSalesPdf(fecha_ini?:any, fecha_fin?:any) {
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/exportPdf';
    const params = new HttpParams()
        .set('fecha_ini', fecha_ini)
        .set('fecha_fin', fecha_fin);
    return this.http.get(URL, {headers, params, responseType:'blob'});
  }

  createSale(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sales';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showSale(sale_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sales/'+sale_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  createCobro(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/receivables';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }
  
  changeState(sale_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sales/'+sale_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

  getCustomers(searchCustomer:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/getCustomers?searchCustomer='+searchCustomer;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  getProducts(searchProduct:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/getProducts?searchProduct='+searchProduct;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  reconsultarSri(id:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/factura/reconsultar/'+id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  getFacturaPDF(id: number) {
    // console.log(id);
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/factura/'+id+'/pdf';
    return this.http.get(URL, {headers: headers, responseType: 'blob'})
    .pipe();
  }

  rePrintFacturaPDF(id: number) {
    // console.log(id);
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/factura/'+id+'/rePrintFacturaPdf';
    return this.http.get(URL, {headers: headers, responseType: 'blob'})
    .pipe();
  }


  getReceivablePDF(id: number) {
    // console.log(id);
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/receivable/'+id+'/pdf';
    return this.http.get(URL, {headers: headers, responseType: 'blob'})
    .pipe();
  }

  rePrintPDF(id: number) {
    // console.log(id);
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/sale/receivable/'+id+'/rePrintPdf';
    return this.http.get(URL, {headers: headers, responseType: 'blob'})
    .pipe();
  }
  
  deleteReceivable(id_receivable:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/receivables/'+id_receivable;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

  getConfigurations(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/configurations';
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

}
