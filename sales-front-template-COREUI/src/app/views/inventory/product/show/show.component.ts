import { ProductService } from './../product.service';
import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router, } from '@angular/router';
// import { URL_BACKEND } from '../../../config/config';
import moment  from 'moment';
import 'moment/locale/es';
// import { SharedModule } from '../../../shared/shared.module';
import { freeSet } from '@coreui/icons';
import { Location } from '@angular/common';
import { SharedModule } from '../../../../shared/shared.module';
// import { SharedModule } from 'src/app/shared/shared.module';

@Component({
  selector: 'app-show',
  imports: [ SharedModule,],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss'
})
export class ShowComponent {
    
    icons = freeSet;

    favoriteColor = '#26ab3c';

    location = inject(Location);
    router = inject(Router);
    productService = inject(ProductService);
    activatedRoute = inject(ActivatedRoute);
    PRODUCT_ID:any;
    PRODUCT:any = signal<any>({});
    tiempo_creacion:any;
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    isFull:boolean = false;

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.PRODUCT_ID = resp.id;
      });
      this.productService.showProduct(this.PRODUCT_ID)
      .subscribe((resp:any) =>  {
        // console.log(resp);
        this.isFull = JSON.stringify(this.PRODUCT()) === '{}';
        this.PRODUCT.set(resp.Product);
        this.tiempo_creacion = moment(this.PRODUCT().created_at).fromNow();
        if(this.PRODUCT().imagen){
          this.imagen_previsualiza = this.PRODUCT().imagen;
        }
      });
    }

    goBack(){
      this.router.navigateByUrl("/inventory/list-product");
    }

    goUpdateProduct(){
      this.router.navigate(['/inventory/edit-product/'+this.PRODUCT_ID]);
    }

    goList(){
      this.router.navigateByUrl("/inventory/list-product");
    }

}
