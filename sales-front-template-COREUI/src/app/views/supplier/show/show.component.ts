import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import moment from 'moment';
import { SharedModule } from '../../../shared/shared.module';
import 'moment/locale/es';
import { Location } from '@angular/common';
import { SupplierService } from '../supplier.service';

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
  supplierService = inject(SupplierService);
  activatedRoute = inject(ActivatedRoute);
  SUPPLIER_ID:any;
  SUPPLIER:any = signal<any>({});
  tiempo_creacion:any;
  imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
  isFull:boolean = false;

  ngOnInit(){
    this.activatedRoute.params.subscribe((resp:any)=>{
      this.SUPPLIER_ID = resp.id;
    });
    this.supplierService.showSupplier(this.SUPPLIER_ID)
    .subscribe((resp:any) =>  {
      this.isFull = JSON.stringify(this.SUPPLIER()) === '{}';
      this.SUPPLIER.set(resp.supplier);
      this.tiempo_creacion = moment(this.SUPPLIER().created_at).fromNow();
    });
  }

  goBack(){
    this.router.navigateByUrl("/supplier/list");
  }

  goUpdateSupplier(){
    this.router.navigate(['/supplier/edit/'+this.SUPPLIER_ID]);
  }

  goList(){
    this.router.navigateByUrl("/supplier/list");
  }

}
