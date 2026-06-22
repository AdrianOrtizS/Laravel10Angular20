import { Injectable, signal  } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { AuthService } from '../../services/auth.service';
import { URL_SERVICIOS } from '../../../config/config';

@Injectable({
  providedIn: 'root'
})
export class IceTarifaService {
  
  constructor(public http: HttpClient,
    public authService: AuthService
  ) { }

  listIceTarifas(page:number=1, search:string, pageSize:number){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/ice_tarifas?page='+page+'&search='+search+'&pageSize='+pageSize;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createIceTarifa(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/ice_tarifas';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showIceTarifa(ice_tarifa_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/ice_tarifas/'+ice_tarifa_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  updateIceTarifa(ice_tarifa_id:string, data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/ice_tarifas/'+ice_tarifa_id;
    return this.http.put(URL, data, {headers: headers})
    .pipe();
  }

  changeState(ice_tarifa_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/ice_tarifas/'+ice_tarifa_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

}
