import { SupplierService } from './../../supplier/supplier.service';
import { ProductService } from './../../product/product.service';
import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { SharedModule } from '../../../shared/shared.module';
import { Router } from '@angular/router';
import { ButtonDirective, FormCheckComponent, FormCheckInputDirective, FormCheckLabelDirective, ListGroupDirective, ListGroupItemDirective, ModalBodyComponent, ModalComponent, ModalHeaderComponent, ModalTitleDirective, ModalToggleDirective } from '@coreui/angular';
import { BuyService } from '../buy.service';

//para crear cliente desde la venta
interface SupplierI {
  name:   string,
  email:  string,
  phone:  string,
  num_identificador:string,
  address: string
}

interface BuyI {
  id_supplier:  any,
  fecha_ingreso:any,
  numero_factura:any,
  type_pay:any,
  type_doc:any,
  subtotal:     any,
  iva:          any,
  total:        any,
  items:        BuyDetalleI[]
}
interface BuyDetalleI {
  id_sale:    any,
  id_product: any,
  quantity:   any,
  price:      any,
  subtotal:   any,
}

@Component({
  selector: 'app-create',
  imports: [SharedModule, ListGroupDirective, ListGroupItemDirective, ButtonDirective, ModalToggleDirective, ModalComponent, ModalHeaderComponent, ModalTitleDirective, ModalBodyComponent, FormCheckComponent, FormCheckInputDirective, FormCheckLabelDirective],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss'
})
export class CreateComponent {

  public favoriteColor = '#26ab3c';
  icons   = freeSet;
  router  = inject(Router);
  toastr  = inject(ToastrService);
  
  supplierService = inject(SupplierService);
  productService  = inject(ProductService);
  buyService     = inject(BuyService);
  
  suppliers:any = signal<any[]>([]);
  products:any  = signal<any[]>([]);
  
  supplierSelect:any  = {};
  productSelect:any   = {};
  
  supplierNew:any = signal<SupplierI>({
    name:   '',
    email:  '',
    num_identificador:'',
    address:'',
    phone:  ''
  });

  search_ngModelSupplier: string =  '';
  search_ngModelProduct:  string =  '';
  configurations:any;
  ivaValor:any;
  fecha_ingreso:any;
  numero_factura:any;
  
  ngOnInit(){
    this.buyService.getConfigurations().subscribe((resp:any)=>{
      this.configurations = resp.configurations;
      this.ivaValor = this.configurations.find((u:any) => u.name === 'iva');
    });
  }

  //Customer
  searchSupplierNull:boolean = false;
  cargarSuppliers(search:any){
    this.searchSupplierNull = false;
    this.buyService.getSuppliers(search)
    .subscribe((resp:any)=>{
      this.suppliers.set(resp.suppliers.data) ;
      this.searchSupplierNull = true;
    });
  }
  //trae clientes de acuerdo al criterio de busqueda
  searchToSupplier(){    
    this.cargarSuppliers(this.search_ngModelSupplier);
    this.search_ngModelSupplier = '';
  }
  selectSupplier(supplier:any){
    this.supplierSelect = supplier;
    this.suppliers.set([]);
    this.search_ngModelSupplier = '';
  }  
  openModalSelectSupplier(){
    this.searchSupplierNull = false;
  }
  

  //Product
  searchProductNull:boolean = false;
  cargarProducts(search:any){
    this.searchProductNull = false;
    this.buyService.getProducts(search)
    .subscribe((resp:any)=>{
      // console.log(resp);
      this.products.set(resp.Products.data);
      this.searchProductNull = true;
    });
  }
  //trae products de acuerdo al criterio de busqueda
  searchToProduct(){    
    this.cargarProducts(this.search_ngModelProduct);
    this.search_ngModelProduct = '';
  }
  selectProduct(product:any){
    this.productSelect = product;
    this.producto.set({ id_product: this.productSelect.id,
                        cod_pro:    this.productSelect.cod_pro, 
                        name:       this.productSelect.name, 
                        quantity:   1, 
                        discount:   0,
                        iva:        0 
                      });
                        
    this.closeModalSelectProduct();
  }  
  openModalSelectProduct(){
    this.searchProductNull = false;
  }
  closeModalSelectProduct(){
    this.toggleLiveDemo();
  }
  
  public visible = false;
  toggleLiveDemo() {
    this.visible = !this.visible;
    this.searchProductNull = false;
    this.products.set([]);
    this.search_ngModelProduct = '';
  }
  handleLiveDemoChange(event: any) {
    this.visible = event;
  }


  // se ejecuta metodo (input) en customer
  updateName(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.supplierNew.update((c:any) => ({ ...c, name: valor }));
  }
  updateEmail(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.supplierNew.update((c:any) => ({ ...c, email: valor }));
  }
  updateNumIdentificador(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.supplierNew.update((c:any) => ({ ...c, num_identificador: valor }));
  }
  updatePhone(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.supplierNew.update((c:any) => ({ ...c, phone: valor }));
  }
  updateAddress(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.supplierNew.update((c:any) => ({ ...c, address: valor }));
  }  
  updateFechaIngreso(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.supplierNew.update((c:any) => ({ ...c, fecha_ingreso: valor }));
  }  
  // Validación de email reactiva
  isEmailValid = computed(() => {
    const email = this.supplierNew().email.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  });
  // Validar si todos los campos son obligatorios y válidos
  isFormValid = computed(() => {
    const c = this.supplierNew();
    return (
      c.name.trim().length > 0 &&
      c.num_identificador.trim().length > 0 &&
      c.phone.trim().length > 0 &&
      c.address.trim().length > 0 &&
      this.isEmailValid()
    );
  });

  saveSupplier(){
    this.supplierService.createSupplier(this.supplierNew())
    .subscribe((resp:any) =>{
      this.supplierSelect = resp.supplier;
      this.suppliers.set([]);
      this.search_ngModelSupplier = '';
      if(resp.code == 403){
        this.toastr.error('Validacion', 'El proveedor ya existe');
        return;
      }
      if(resp.code == 200){
        this.supplierNew().name = '';
        this.supplierNew().surname = '';
        this.supplierNew().num_identificador = '';
        this.supplierNew().email = '';
        this.supplierNew().phone = '';
        this.supplierNew().address = '';
        this.supplierNew().fecha_ingreso = '';

        this.toastr.success('Exito', 'El proveedor se ha creado correctamente');
        return;
      }

    });
  }


  // Métodos para update PRODUCT cada campo (evita parser error)
  updateProductName(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.producto.update((c:any) => ({ ...c, name: valor }));
  }
  updateProductPrice(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.producto.update((c:any) => ({ ...c, price: valor }));
  }
  updateProductQuantity(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.producto.update((c:any) => ({ ...c, quantity: valor }));
  }
  updateProductDiscount(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.producto.update((c:any) => ({ ...c, discount: valor }));
  }

  // Validar si los campos cantidad y precio son válidos
  isAddProductValid = computed(() => {
    const p = this.producto();
    return (
      p.quantity > 0 &&
      p.price > 0
    );
  });

  isBuyValid(){
    return (
      this.supplierSelect.id > 0 &&
      this.fecha_ingreso &&
      this.numero_factura &&
      this.items.length > 0
    );
  }

  formatSerie(){
    if(this.numero_factura.length == 3 || this.numero_factura.length == 7){
      this.numero_factura = this.numero_factura+'-';
      // console.log(this.numero_factura);
    }
  }
  
  buy: BuyI = {
    id_supplier:  0,
    fecha_ingreso:'',
    numero_factura: '',
    subtotal:     0,
    type_pay: 1,
    type_doc: 1,
    // discount:     0,
    iva:          0,
    total:        0,
    items:        []
  };

  producto:any = signal<any>({
    id_product: 0,
    cod_pro:    '',
    name:       '',
    quantity:   1,
    price:      0,
    subtotal:   0,
    iva:        0,
    // discount:   0
  });
  items: any[] = [];


  agregarProducto() {
    this.producto().subtotal = this.producto().price * this.producto().quantity;
    this.producto().iva = Number(((this.producto().subtotal*this.ivaValor.value)/100).toFixed(2));
    this.items.push({ ...this.producto() });
    this.producto.set({ 
      id_product: '',
      cod_pro:    '', 
      name:       '', 
      quantity:   1, 
      price:      0, 
      subtotal:   0, 
      iva:        0,
      // discount:   0 
    });
  }

  selectedTypeBuy: string = '1'; // Valor por defecto
  selectedTypeDoc: string = '1'; // Valor por defecto
  // Opcional: función para hacer algo cuando cambia
  onTypeBuyChange() {
    // console.log('Tipo de compra seleccionado:', this.selectedTypeBuy);
  }
  onTypeDocChange() {
    // console.log('Tipo de documento seleccionado:', this.selectedTypeDoc);
  }

  save(){
    let iva = 0;
    if(this.selectedTypeDoc == '1'){  //1 contado - 2 credito
      iva = this.ivaFac;              //1 fact - 2 not. venta   
    }                                  
    this.buy = {
      id_supplier:    this.supplierSelect.id,
      fecha_ingreso:  this.fecha_ingreso, 
      numero_factura: this.numero_factura,
      type_pay:     this.selectedTypeBuy, //1 contado - 2 credito
      type_doc:     this.selectedTypeDoc, //1 fact - 2 not. venta
      subtotal:     this.subtotal,
      iva:          iva,
      total:        this.total,
      items:        this.items
    };

    this.buyService.createBuy(this.buy)
    .subscribe((resp:any) =>{
      if(resp.code == 403){
        const messageError: any[] = Object.values(resp.message);
        if(messageError[0][0].includes("supplier")) {
          this.toastr.error('Validacion', 'Selecciona proveedor');
          return;
        }
        if(messageError[0][0].includes("items")) {
          this.toastr.error('Validacion', 'Selecciona al menos un producto');
          return;
        }
      }

      if(resp.code == 405){
        this.toastr.error('Error', 'No se pudo realizar la compra '+resp.errors);
      }
      if(resp.code == 500){
        this.toastr.error('Error', 'Error al intentar conectarse al servicio de SRI, verifique su conexion a internet');
      }
      if(resp.code == 200){
        this.toastr.success('Exito', 'La compra se ha creado correctamente');
        
        setTimeout(() => {
          this.supplierSelect = {};
          this.subtotal = 0;
          this.total = 0;
          this.selectedTypeBuy = '1';   //1 contado - 2 credito
          this.selectedTypeDoc = '1';   //1 fact - 2 not. venta
          this.fecha_ingreso ='';
          this.numero_factura = '';
          this.items = [];
        }, 1000);
      }
      return;
    });
  }


  eliminarProducto(item: any) {
    this.items = this.items.filter(p => p !== item);
  }

  subtotal:any  = 0;
  ivaFac:any       = 0;
  total:any     = 0;
  
  calcularSubtotal() {
    this.subtotal = 0;
    this.subtotal = this.items.reduce((subtotal, p) => subtotal + (p.quantity * p.price), 0);
    return this.subtotal;
  }
  calcularIva() {
    if(this.ivaValor){
      this.ivaFac = (this.subtotal * this.ivaValor.value)/100;
      return this.ivaFac;
    }
  }
  calcularTotal() {
    if(this.selectedTypeDoc == '1'){
      this.total = this.subtotal + this.ivaFac;
      return this.total;
    }
    if(this.selectedTypeDoc == '2'){
      this.total = this.subtotal ;
      return this.total;
    }
  }

  goList(){
    this.router.navigateByUrl("/buy/list");
  }

}
