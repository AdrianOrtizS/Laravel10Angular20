// import { SharedModule } from './../../../shared/shared.module';
import { ProductService } from './../product.service';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute, Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { Component, computed, effect, inject, signal } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { FormSelectDirective } from '@coreui/angular';
import { SharedModule } from '../../../../shared/shared.module';
// import { SharedModule } from 'src/app/shared/shared.module';
// import { error } from 'console';

interface ProductI {
  cod_pro: string,
  name:    string,
  description: string,
  price:  number,
  stock:  number,
  stock_min: number,
  imagen: string,
  id_categorie: number,
  // state: any
}

@Component({
  selector: 'app-edit',
  imports: [ SharedModule, FormSelectDirective, ReactiveFormsModule ],
  templateUrl: './edit.component.html',
  styleUrl: './edit.component.scss',
  host: {
    'class': 'example',
  },
})
export class EditComponent {

    id_categorieTouched  = false;
    cod_proTouched = false;
    nameTouched    = false;
    descriptionTouched = false;
    priceTouched   = false;
    stockTouched   = false;
    stock_minTouched = false;
    imagenTouched  = false;

    public favoriteColor = '#26ab3c';
    icons   = freeSet;
    router  = inject(Router);
    toastr  = inject(ToastrService);
    productService = inject(ProductService);
    activatedRoute = inject(ActivatedRoute);

    /**
     *
     */
    // constructor() {
    //   effect(() => {
    //     console.log('Producto:', this.PRODUCT());
    //   });
    // }
    // PRODUCT:any = signal<any>({});
    
    PRODUCT:any = signal<ProductI>({
      cod_pro: '',
      name:    '',
      description:  '',
      price:  0,
      stock:  0,
      stock_min: 0,
      imagen: '',
      id_categorie: 0
    });

    PRODUCT_ID:any  = null;

    Categories:any = [];
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    file_imagen:any =null;
    isEmpty:boolean = true;
    
    ngOnInit(){
      this.productService.getCategories()
      .subscribe((resp:any)=>{
        this.Categories = resp.Categories;
        // console.log(this.Categories);
      });
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.PRODUCT_ID = resp.id;
      });
      this.productService.showProduct(this.PRODUCT_ID)
      .subscribe((resp:any)=>{
        
        this.isEmpty = Object.keys(this.PRODUCT()).length === 0;
        this.PRODUCT.set(resp.Product);
        
        // console.log(this.PRODUCT());
        

        if(this.PRODUCT().imagen){
          this.imagen_previsualiza = this.PRODUCT().imagen;
        }
      });
    }

    clickInputFileHide(){
      const clickInputFile = document.getElementById('productImage');
      clickInputFile?.click();
    }

    processFile($event:any){
      if($event.target.files[0].type.indexOf('image') < 0){
        return;
      }
      this.file_imagen = $event.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(this.file_imagen);
      reader.onloadend = ()=> this.imagen_previsualiza = reader.result;
    }

    // Métodos para update cada campo (evita parser error)
    updateCod(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, cod_pro: valor }));
    }    
    updateName(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, name: valor }));
    }

    updateDescription(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, description: valor }));
    }

    updatePrice(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, price: valor }));
    }

    updateStock(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, stock: valor }));
    }
    updateStockMin(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, stock_min: valor }));
    }
    
    // updateId_categorie(event: Event) {
    //   const valor = (event.target as HTMLInputElement).value;
    //   this.PRODUCT.update((c:any) => ({ ...c, id_categorie: valor }));
    // }
    updateId_categorie(value: number) {
      this.PRODUCT.update((c:any) => ({
        ...c,
        id_categorie: Number(value)
      }));
    }

    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.PRODUCT();
      return (
        c.cod_pro.trim().length > 0 &&
        c.name.trim().length > 0 &&
        c.description.trim().length > 0 &&
        
        (c.price != '' && c.price >= 0) && 
        c.stock != '' &&
        c.stock_min != '' &&
        c.id_categorie > 0
      );
    });


    save(){
      if(this.PRODUCT().id_categorie == 0){
        this.toastr.error('Validacion', 'Seleccione categoria');
        return;
      }
      
      let formData = new FormData();
      formData.append('cod_pro',    this.PRODUCT().cod_pro);
      formData.append('name',       this.PRODUCT().name);
      formData.append('description', this.PRODUCT().description);
      formData.append('price',      this.PRODUCT().price);
      formData.append('stock',       parseInt(this.PRODUCT().stock).toString());
      formData.append('stock_min',    parseInt(this.PRODUCT().stock_min).toString());
      formData.append('id_categorie', this.PRODUCT().id_categorie);

      if(this.file_imagen){
        formData.append('producto', this.file_imagen);
      }

      this.productService.updateProduct(this.PRODUCT_ID, formData)
      .subscribe({
        next:() =>{
          this.toastr.success('Exito', 'La producto se ha actualizado correctamente');
        },
        error:(error) =>{
          console.log(error.error.errors);
          this.toastr.error('Error', error.error.message);
        }
      });
    }

    goList(){
      this.router.navigateByUrl("/inventory/list-product");
    }

}
