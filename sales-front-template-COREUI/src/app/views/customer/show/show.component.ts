import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import moment from 'moment';
import { CustomerService } from '../customer.service';
import { SharedModule } from '../../../shared/shared.module';
// import moment  from 'moment';
// import 'moment/locale/es';
import { Location } from '@angular/common';

@Component({
  selector: 'app-show',
  imports: [ SharedModule ],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss'
})
export class ShowComponent {

  icons = freeSet;

  favoriteColor = '#26ab3c';

  location = inject(Location);
  router = inject(Router);
  customerService = inject(CustomerService);
  activatedRoute = inject(ActivatedRoute);
  CUSTOMER_ID:any;
  CUSTOMER:any = signal<any>({});
  tiempo_creacion:any;
  imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
  isFull:boolean = false;

  ngOnInit(){
    this.activatedRoute.params.subscribe((resp:any)=>{
      console.log(resp);
      this.CUSTOMER_ID = resp.id;
    });
    this.customerService.showCustomer(this.CUSTOMER_ID)
    .subscribe((resp:any) =>  {
      this.isFull = JSON.stringify(this.CUSTOMER()) === '{}';
      this.CUSTOMER.set(resp.Customer);
      console.log(this.CUSTOMER());
      this.tiempo_creacion = moment(this.CUSTOMER().created_at).fromNow();
      
    });
  }

  goBack(){
    this.router.navigateByUrl("/customer/list");
  }

  goUpdateCustomer(){
    this.router.navigate(['/customer/edit/'+this.CUSTOMER_ID]);
  }

  goList(){
    this.router.navigateByUrl("/customer/list");
  }

  goListSales(){
    console.log(this.CUSTOMER_ID);
    this.router.navigate(['/customer/sales_history/'+this.CUSTOMER_ID]);
  }

}
