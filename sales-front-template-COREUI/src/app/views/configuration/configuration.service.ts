import { AuthService } from '../services/auth.service';
import { URL_SERVICIOS } from '../../config/config';
import { Injectable, signal  } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';


@Injectable({
  providedIn: 'root'
})
export class ConfigurationService {

  constructor(public http: HttpClient,
    public authService: AuthService
  ) { }

  listConfigurations(page:number=1, search:string, pageSize:number){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/configurations?page='+page+'&search='+search+'&pageSize='+pageSize;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createConfiguration(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/configurations';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showConfiguration(configuration_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/configurations/'+configuration_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  updateConfiguration(configuration_id:string, data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/configurations/'+configuration_id;
    return this.http.put(URL, data, {headers: headers})
    .pipe();
  }

  changeState(configuration_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/configurations/'+configuration_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

  getTarifasIva(){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/getTarifasIva';
    return this.http.get(URL, {headers: headers})
    .pipe();
  }
}
