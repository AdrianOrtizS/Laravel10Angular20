// import { Injectable } from '@angular/core';
import { AuthService } from './../services/auth.service';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Injectable } from '@angular/core';
import { URL_SERVICIOS } from '../../config/config';

@Injectable({
  providedIn: 'root',
})
export class UserService {
  
    constructor(public http: HttpClient,
      public authService: AuthService
    ) { }

    getBranches(){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/getBranches';
      return this.http.get(URL, {headers: headers})
              .pipe();
    }

    getPointsOfSale(id_branch:any){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/getPointsOfSale?id_branch='+id_branch;
      return this.http.get(URL, {headers: headers})
              .pipe();
    }
    
    listUsers(page:number=1, search:string, pageSize:number){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/users?page='+page+'&search='+search+'&pageSize='+pageSize;
      return this.http.get(URL, {headers: headers})
              .pipe();
    }
  
    // createUser(data:any){
    //   let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
    //   let URL = URL_SERVICIOS+'/users';
    //   return this.http.post(URL, data, {headers: headers})
    //   .pipe();
    // }
  
    showUser(User_id:string){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/users/'+User_id;
      return this.http.get(URL, {headers: headers})
      .pipe();
    }
  
    updateUser(User_id:string, data:any){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/users/'+User_id;
      return this.http.put(URL, data, {headers: headers})
      .pipe();
    }
  
    changeState(User_id:string){
      let headers = new HttpHeaders({'Authorization': 'Bearer '+JSON.parse(this.authService.token)});
      let URL = URL_SERVICIOS+'/users/'+User_id;
      return this.http.delete(URL, {headers: headers})
      .pipe();
    }

}
