// import { ProductService } from './../../product/product.service';
import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { SharedModule } from '../../../shared/shared.module';
import { Router } from '@angular/router';
import { SaleService } from '../sale.service';
import { ButtonDirective, FormCheckComponent, FormCheckInputDirective, FormCheckLabelDirective, ListGroupDirective, ListGroupItemDirective, ModalBodyComponent, ModalComponent, ModalHeaderComponent, ModalTitleDirective, ModalToggleDirective } from '@coreui/angular';
import { CustomerService } from '../../customer/customer.service';
import { ProductService } from '../../inventory/product/product.service';

//para crear cliente desde la venta
interface CustomerI {
  name:   string,
  email:  string,
  phone:  string,
  num_identificador:string,
  address: string
}

interface SaleI {
  id_customer:  any,
  subtotal:     any,
  discount:     any,
  type_receivable:any,
  iva:          any,
  total:        any,
  items:        SaleDetalleI[]
}
interface SaleDetalleI {
  id_sale:    any,
  id_product: any,
  quantity:   any,
  price:      any,
  subtotal:   any,
  discount:   any
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
  
  customerService = inject(CustomerService);
  productService  = inject(ProductService);
  saleService     = inject(SaleService);
  
  customers:any = signal<any[]>([]);
  products:any  = signal<any[]>([]);
  
  customerSelect:any  = {};
  productSelect:any   = {};

  customerNew:any = signal<CustomerI>({
    name:   '',
    email:  '',
    num_identificador:'',
    address:'',
    phone:  ''
  });

  search_ngModelCustomer: string =  '';
  search_ngModelProduct:  string =  '';
  configurations:any;
  ivaValor:any;

  ngOnInit(){
    this.saleService.getConfigurations().subscribe((resp:any)=>{
      this.configurations = resp.configurations;
      this.ivaValor = this.configurations.find((u:any) => u.name === 'iva');
    });
  }

  selectedTypeSale: string = '1'; // Valor por defecto
  // Opcional: función para hacer algo cuando cambia
  onTypeSaleChange() {
    // console.log('Tipo de compra seleccionado:', this.selectedTypeBuy);
  }
  

  //Customer
  searchCustomerNull:boolean = false;
  cargarCustomers(search:any){
    this.searchCustomerNull = false;
    this.saleService.getCustomers(search)
    .subscribe((resp:any)=>{
      this.customers.set(resp.customers.data) ;
      this.searchCustomerNull = true;
    });
  }
  //trae clientes de acuerdo al criterio de busqueda
  searchToCustomer(){    
    this.cargarCustomers(this.search_ngModelCustomer);
    this.search_ngModelCustomer = '';
  }
  selectCustomer(customer:any){
    this.customerSelect = customer;
    this.customers.set([]);
    this.search_ngModelCustomer = '';
  }  
  openModalSelectCustomer(){
    this.searchCustomerNull = false;
  }
  

  //Product
  searchProductNull:boolean = false;
  cargarProducts(search:any){
    this.searchProductNull = false;
    this.saleService.getProducts(search)
    .subscribe((resp:any)=>{

      console.log(resp);
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
    // console.log(product);
    this.productSelect = product;
    this.producto.set({ id_product: this.productSelect.id,
                        cod_pro:    this.productSelect.cod_pro, 
                        name:       this.productSelect.name, 
                        quantity:   1, 
                        price:      this.productSelect.price, 
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
    this.customerNew.update((c:any) => ({ ...c, name: valor }));
  }
  updateEmail(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.customerNew.update((c:any) => ({ ...c, email: valor }));
  }
  updateNumIdentificador(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.customerNew.update((c:any) => ({ ...c, num_identificador: valor }));
  }
  updatePhone(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.customerNew.update((c:any) => ({ ...c, phone: valor }));
  }
  updateAddress(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.customerNew.update((c:any) => ({ ...c, address: valor }));
  }  
  // Validación de email reactiva
  isEmailValid = computed(() => {
    const email = this.customerNew().email.trim();
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
  });
  // Validar si todos los campos son obligatorios y válidos
  isFormValid = computed(() => {
    const c = this.customerNew();
    return (
      c.name.trim().length > 0 &&
      c.num_identificador.trim().length > 0 &&
      c.phone.trim().length > 0 &&
      c.address.trim().length > 0 &&
      this.isEmailValid()
    );
  });

  saveCustomer(){
    this.customerService.createCustomer(this.customerNew())
    .subscribe((resp:any) =>{
      this.customerSelect = resp.customer;
      this.customers.set([]);
      this.search_ngModelCustomer = '';
      if(resp.code == 403){
        this.toastr.error('Validacion', 'El cliente ya existe');
        return;
      }
      if(resp.code == 200){
        this.customerNew().name = '';
        this.customerNew().surname = '';
        this.customerNew().num_identificador = '';
        this.customerNew().email = '';
        this.customerNew().phone = '';
        this.customerNew().address = '';

        this.toastr.success('Exito', 'El cliente se ha creado correctamente');
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

  isSaleValid(){
    return (
      this.customerSelect.id > 0 &&
      this.items.length > 0
    );
  }


  sale: SaleI = {
    id_customer:  0,
    subtotal:     0,
    discount:     0,
    iva:          0,
    total:        0,
    type_receivable : 1,
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
    discount:   0
  });
  items: any[] = [];


  agregarProducto() {
    this.producto().subtotal = this.producto().price * this.producto().quantity;
    this.producto().discount = ((this.producto().discount * this.producto().price)/100)*this.producto().quantity;
    // console.log(this.ivaValor.value);
    this.producto().iva = Number((((this.producto().subtotal-this.producto().discount)*this.ivaValor.value)/100).toFixed(2));
    // console.log(this.producto());

    this.items.push({ ...this.producto() });
    this.producto.set({ 
      id_product: '',
      cod_pro:    '', 
      name:       '', 
      quantity:   1, 
      price:      0, 
      subtotal:   0, 
      iva:        0,
      discount:   0 
    });
  }


  save(){
    this.sale = {
      id_customer:  this.customerSelect.id,
      type_receivable:  this.selectedTypeSale, //1 contado - 2 credito
      subtotal:     this.subtotal,
      discount:     this.descuentoTotal,
      iva:          this.ivaFac,
      total:        this.total,
      items:        this.items
    };
// console.log(this.sale);
    this.saleService.createSale(this.sale)
    .subscribe((resp:any) =>{
      console.log(resp);
      if(resp.resp.code == 403){
        const messageError: any[] = Object.values(resp.message);
        if(messageError[0][0].includes("customer")) {
          this.toastr.error('Validacion', 'Selecciona cliente');
          return;
        }
        if(messageError[0][0].includes("items")) {
          this.toastr.error('Validacion', 'Selecciona al menos un producto');
          return;
        }
      }

      if(resp.resp.code == 405){
        this.toastr.error('Error', 'No se pudo realizar la venta');
      }
      if(resp.resp.code == 500){
        this.toastr.error('Error', 'Error al intentar conectarse al servicio de SRI, verifique su conexion a internet');
      }
      if(resp.resp.code == 200){
        this.toastr.success('Exito', 'La venta se ha creado correctamente');
        
        this.printSale(resp.sale.id);
        
        let mailCustomerSale = resp.sale.customer.email;
        let claveAcceso = resp.sale.clave_acceso;
        this.saleService.sendFacturaPdfXml(claveAcceso, mailCustomerSale, resp.sale)
          .subscribe((resp:any)=>{
            this.toastr.success(resp.message);
          });
          
        setTimeout(() => {
          this.customerSelect = {};
          this.subtotal = 0;
          this.total = 0;
          this.descuentoTotal = 0;
          this.items = [];
        }, 2500);

      }
      return;
    });
  }


  eliminarProducto(item: any) {
    this.items = this.items.filter(p => p !== item);
  }

  subtotal:any  = 0;
  descuentoTotal:any = 0;
  ivaFac:any       = 0;
  total:any     = 0;
  
  calcularSubtotal() {
    this.subtotal = this.items.reduce((subtotal, p) => subtotal + (p.quantity * p.price), 0);
    return this.subtotal;
  }
  calcularDescuentoTotal() {
    this.descuentoTotal = this.items.reduce((discount, p) => discount + p.discount, 0);
    return this.descuentoTotal;
  }
  calcularIva() {
    if(this.ivaValor){
      this.ivaFac = ((this.subtotal - this.descuentoTotal) * this.ivaValor.value)/100;
      return this.ivaFac;
    }
  }
  calcularTotal() {
    this.total = (this.subtotal - this.descuentoTotal) + this.ivaFac;
    return this.total;
  }

  printSale(id_sale:any){
    this.saleService.getFacturaPDF(id_sale).subscribe((pdfBlob: Blob) => {
        const url = window.URL.createObjectURL(pdfBlob);
        const newWindow = window.open(url, '_blank');
        if (newWindow) {
          newWindow.print(); // abre el diálogo de impresión directamente
        }
    });
  }
   
  ticketSale(id_sale:any){
    this.router.navigateByUrl(`/sale/ticket/${id_sale}`);
  }

  goList(){
    this.router.navigateByUrl("/sale/list");
  }

}
