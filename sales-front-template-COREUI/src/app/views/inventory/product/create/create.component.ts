import { SharedModule } from './../../../../shared/shared.module';
import { ProductService } from './../product.service';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { FormSelectDirective } from '@coreui/angular';

interface ProductI {
  cod_pro:string,
  name:   string,
  description: string,
  price:  string,
  stock:  string,
  stock_min:  string,
  imagen: string,
  id_categorie: number,
  id_tarifa_iva: number
}
        
@Component({
  selector: 'app-create',
  imports: [  SharedModule, FormSelectDirective, ReactiveFormsModule  ],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss'
})
export class CreateComponent {

    id_categorieTouched  = false;
    cod_proTouched = false;
    nameTouched    = false;
    descriptionTouched = false;
    priceTouched   = false;
    stockTouched   = false;
    stock_minTouched = false;
    imagenTouched  = false;
    id_tarifa_ivaTouched  = false;


    public favoriteColor = '#26ab3c';
    icons = freeSet;
    router = inject(Router);
    toastr  = inject(ToastrService);
    productService = inject(ProductService);
    
    PRODUCT:any = signal<ProductI>({
      cod_pro:  '',
      name:     '',
      description:  '',
      price:  '',
      stock:  '',
      stock_min:'',
      imagen: '',
      id_categorie: 0,
      id_tarifa_iva: 0
    });

    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    
    file_imagen:any = null;
    Categories:any  = [];
    Tarifas_iva:any  = [];

    ngOnInit(){
      this.productService.getCategories()
      .subscribe((resp:any)=>{
        this.Categories = resp.Categories;
      });
      this.productService.getTarifasIva()
      .subscribe((resp:any)=>{
        this.Tarifas_iva = resp.Tarifas_iva;
        console.log(this.Tarifas_iva);
      });
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
    
    updateId_categorie(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, id_categorie: valor }));
    }

    updateId_tarifa_iva(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.PRODUCT.update((c:any) => ({ ...c, id_tarifa_iva: valor }));
    }

    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.PRODUCT();
      return (
        c.cod_pro.trim().length > 0 &&
        c.name.trim().length > 0 &&
        c.description.trim().length > 0 &&
        (c.price >=0 && c.price.trim().length > 0) &&
        c.stock.trim().length > 0 &&
        c.stock_min.trim().length > 0 && 
        c.id_tarifa_iva > 0 && 
        c.id_categorie > 0
      );
    });

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
      reader.onloadend = () => this.imagen_previsualiza = reader.result;
    }


    save(){

      if(this.PRODUCT.id_categorie == 0){
        this.toastr.error('Validacion', 'Seleccione categoria');
        return;
      }

      let formData = new FormData();
      formData.append('cod_pro',  this.PRODUCT().cod_pro);
      formData.append('name',     this.PRODUCT().name);
      formData.append('description', this.PRODUCT().description);
      formData.append('price', this.PRODUCT().price);
      formData.append('stock', this.PRODUCT().stock);
      formData.append('stock_min', this.PRODUCT().stock_min);
      formData.append('id_tarifa_iva', this.PRODUCT().id_tarifa_iva);
      formData.append('id_categorie', this.PRODUCT().id_categorie);

      if(this.file_imagen){
        formData.append('producto', this.file_imagen);
      }

      this.productService.createProduct(formData)
      .subscribe((resp:any) =>{
        console.log(resp);
        if(resp.code == 403){
          this.toastr.error('Validacion', 'Error al crear producto');
          return;
        }
        this.toastr.success('Exito', 'La producto se ha creado correctamente');
        this.limpiarFormulario();
      });
    }

    // Limpiar formulario
    limpiarFormulario() {
      this.id_categorieTouched  = false;
      this.cod_proTouched = false;
      this.nameTouched    = false;
      this.descriptionTouched = false;
      this.priceTouched   = false;
      this.stockTouched   = false;
      this.stock_minTouched = false;
      this.imagenTouched  = false;
      this.id_tarifa_ivaTouched  = false;

      this.PRODUCT.set({ 
        cod_pro:'',
        name:   '', 
        description: '',
        id_categorie:  0, 
        price:  '',
        stock:'',
        stock_min:'',
        id_tarifa_iva: 0
      });
      this.file_imagen = null;
      this.imagen_previsualiza = '../../../../assets/images/sin_imagen.jpg';        
    }

    goList(){
      this.router.navigateByUrl("/inventory/list-product");
    }

}
