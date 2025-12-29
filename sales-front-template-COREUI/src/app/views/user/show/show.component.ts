import { UserService } from './../user.service';
import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import moment from 'moment';
import { SharedModule } from '../../../shared/shared.module';

@Component({
  selector: 'app-show',
  imports: [SharedModule],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss',
})
export class ShowComponent {

  icons = freeSet;

  favoriteColor = '#26ab3c';

  // location = inject(Location);
  router = inject(Router);
  userService = inject(UserService);
  activatedRoute = inject(ActivatedRoute);

  // flush:any;

  USER_ID:any;
  USER:any = signal<any>({});
  tiempo_creacion:any;
  imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
  isFull:boolean = false;

  ngOnInit(){
    this.activatedRoute.params.subscribe((resp:any)=>{
      this.USER_ID = resp.id;
    });
    this.userService.showUser(this.USER_ID)
    .subscribe((resp:any) =>  {
      // console.log(resp);
      this.isFull = JSON.stringify(this.USER()) === '{}';
      this.USER.set(resp.User);
      console.log(this.USER());
      this.tiempo_creacion = moment(this.USER().created_at).fromNow();
    });
  }

  goBack(){
    this.router.navigateByUrl("/user/list");
  }

  goUpdateUser(){
    this.router.navigate(['/user/edit/'+this.USER_ID]);
  }

  goList(){
    this.router.navigateByUrl("/user/list");
  }

}
