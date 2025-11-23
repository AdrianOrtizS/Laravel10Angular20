import { AuthService } from './../services/auth.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root'
})
export class BranchService {

  constructor(public http: HttpClient,
    public authService: AuthService
  ) { }

  listBranches(page:number=1, search:string, pageSize:number){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/branches?page='+page+'&search='+search+'&pageSize='+pageSize;
    return this.http.get(URL, {headers: headers})
            .pipe();
  }

  createBranch(data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/branches';
    return this.http.post(URL, data, {headers: headers})
    .pipe();
  }

  showBranch(branch_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/branches/'+branch_id;
    return this.http.get(URL, {headers: headers})
    .pipe();
  }

  updateBranch(branch_id:string, data:any){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/branches/'+branch_id;
    return this.http.put(URL, data, {headers: headers})
    .pipe();
  }

  changeState(branch_id:string){
    let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    let URL = URL_SERVICIOS+'/branches/'+branch_id;
    return this.http.delete(URL, {headers: headers})
    .pipe();
  }

}
