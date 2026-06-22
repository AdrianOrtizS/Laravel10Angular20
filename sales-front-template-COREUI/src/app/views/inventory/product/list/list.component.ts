import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, inject, signal } from '@angular/core';
import { ProductService } from '../product.service';
import { Router } from '@angular/router';
import { SharedModule } from '../../../../shared/shared.module';
import { URL_BACKEND } from '../../../../config/config';

@Component({
  selector: 'app-list',
  imports: [ SharedModule ],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss'
})
export class ListComponent {

  icons = freeSet;

  productService = inject(ProductService);
  router  = inject(Router);
  toastr  = inject(ToastrService);
  
  name:   string='';
  description: string='';
  imagen: string='';
  state:  boolean=true;
  price:  number=0;
  stock:  number=0;
  id_categorie: number=0;
  cod_pro: string='';
  imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
  tarifa_iva:any;

  products:   any = signal<any[]>([]);
  search:     string ='';
  totalPages: number =0;
  currentPage:number =1;
  pageSize:   number = 5;

  itemForPage:any = [
    {'id':1,'val':1},
    {'id':2,'val':2},
    {'id':5,'val':5},
    {'id':10,'val':10},
    {'id':20,'val':20}];

  // modalId = signal<number | null>(null);
  selecteProduct = signal<any | null>(null);

  constructor(
  ){
  }

  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }

  ngOnInit(){
    this.listarProducts();
  }

  refresh(){
    this.search = '';
    this.listarProducts(1);  
  }

  verifData = 0;
  listarProducts(page = 1){
    this.productService.listProducts(page, this.search, this.id_categorie, this.pageSize)
    .subscribe((resp:any) => {
      // console.log(resp);
      this.verifData = 0;
      if(resp.total == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      return this.products.set(resp.Products.data) ;
    });
  }


  searchTo(){
    this.listarProducts();
  }

  loadPage($event:any){
    this.listarProducts($event);
  }
  
  getImageUrl(imagePath: string | null): string {
    return imagePath 
      ? `${URL_BACKEND}storage/${imagePath}` 
      : `${URL_BACKEND}uploads/sin_imagen.jpg`;
  }

  openModalState(id: number) {
    this.selecteProduct.set(id);
  }

  closerModalState() {
    this.selecteProduct.set(null);
  }

  changeState(Product_id:any){
    this.productService.changeState(Product_id)
    .subscribe((resp:any) => {
      this.closerModalState();
      let state= false;
      if(resp[1] === 'Product deactivate'){
        state = false;
        this.toastr.success('Exito', 'La Product se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'La Product se ha activado correctamente');
      }
      this.updateProduct(Product_id, state);
    });
  }

  // this.updateProduct(Product_id, state);
  updateProduct(id: number, state: boolean) {
    this.products.update((lista:any) =>
      lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
    );
  }

  createProduct(){
    this.router.navigateByUrl("/inventory/create-product");
  }

  editProduct(item:any){
    this.router.navigateByUrl("/inventory/edit-product/"+item.id);
  }

  viewProduct(item:any){
    this.router.navigateByUrl("/inventory/show-product/"+item.id);
  }

}
