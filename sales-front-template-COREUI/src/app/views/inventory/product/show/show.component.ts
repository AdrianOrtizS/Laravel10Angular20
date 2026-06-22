import { ProductService } from './../product.service';
import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router, } from '@angular/router';
import moment  from 'moment';
import { freeSet } from '@coreui/icons';
import { Location } from '@angular/common';
import { SharedModule } from '../../../../shared/shared.module';

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
    // configurationService = inject(ConfigurationService);

    activatedRoute = inject(ActivatedRoute);
    PRODUCT_ID:any;
    PRODUCT:any = signal<any>({});
    tiempo_creacion:any;
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    isFull:boolean = false;
    ivaValor:any;
    configurations:any;

    ngOnInit(){
      this.productService.getConfigurations().subscribe((resp:any)=>{
        this.configurations = resp.configurations;
        this.ivaValor = this.configurations.find((u:any) => u.name === 'iva');
        // console.log(this.ivaValor);
      }); 
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.PRODUCT_ID = resp.id;
      });
      
      this.productService.showProduct(this.PRODUCT_ID)
      .subscribe((resp:any) =>  {
        this.isFull = JSON.stringify(this.PRODUCT()) === '{}';
        this.PRODUCT.set(resp.Product);
        this.tiempo_creacion = moment(this.PRODUCT().created_at).fromNow();
        if(this.PRODUCT().imagen){
          this.imagen_previsualiza = this.PRODUCT().imagen;
        }
        // console.log(this.PRODUCT());
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
