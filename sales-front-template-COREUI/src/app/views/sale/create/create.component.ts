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
  iva0:          any,
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
                        iva: this.productSelect.tarifa_iva.porcentaje 
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
  updateIva(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.producto.update((c:any) => ({ ...c, iva: valor }));
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
    iva0:         0,
    iva:        0,
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
    iva: 0,
    discount:   0
  });
  items: any[] = [];


  baseImponible:any = 0;
  agregarProducto() {
    
    this.producto().subtotal = this.producto().price * this.producto().quantity;
    this.producto().discount = ((this.producto().discount * this.producto().price)/100)*this.producto().quantity;
    this.producto().iva = Number((((this.producto().subtotal-this.producto().discount)*this.producto().iva)/100).toFixed(2));
    
    this.baseImponible = this.producto().subtotal - this.producto().discount;

    if(this.producto().iva != 0){
      // console.log('tarifa 15');
      this.producto().impuesto = { 'codigo'    : '2',   //  2-iva, 3-ice, 5-IRBPNR
                                    'codigoPorcentaje' : '4', //0   0%
                                                              //2   12% (antiguo)
                                                              //4   15% (actual Ecuador)
                                                              //6   No objeto
                                                              //7   Exento
                                    'tarifa'           : this.ivaValor.value,  // desde BD
                                    'baseImponible'    : this.baseImponible,
                                    'valor': this.producto().iva.toFixed(2) };
    }else{
      // console.log('tarifa 0');
      this.producto().impuesto = { 'codigo'    : '2',   //  2-iva, 3-ice, 5-IRBPNR
                                    'codigoPorcentaje' : '0', //0   0%
                                                              //2   12% (antiguo)
                                                              //4   15% (actual Ecuador)
                                                              //6   No objeto
                                                              //7   Exento
                                    'tarifa'           : '0',  // desde BD
                                    'baseImponible'    : this.baseImponible,
                                    'valor': this.producto().iva.toFixed(2) };
    }

    
    this.items.push({ ...this.producto() });
    // console.log(this.items);

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
    this.calcularTotales();
  }

  save(){
    this.sale = {
      id_customer:    this.customerSelect.id,
      type_receivable:  this.selectedTypeSale, //1 contado - 2 credito
      subtotal:     this.subtotal,
      discount:     this.descuentoTotal,
      iva0:         this.iva0,
      iva:          this.iva,
      total:        this.total,
      items:        this.items
    };


    this.saleService.createSale(this.sale)
      .subscribe({
        next: (resp: any) => {
          // console.log(resp);
          this.toastr.success('Éxito', 'La venta se ha creado correctamente');

          this.printSale(resp.sale.id);

          // let mailCustomerSale = resp.sale.customer.email;
          // let claveAcceso = resp.sale.clave_acceso;

          // this.saleService.sendFacturaPdfXml(claveAcceso, mailCustomerSale, resp.sale)
          //   .subscribe((resp: any) => {
          //     this.toastr.success(resp.message);
          //   });

          setTimeout(() => {
            this.customerSelect = {};
            this.subtotal       = 0;
            this.total          = 0;
            this.iva            = 0;
            this.iva0           = 0;
            this.descuentoTotal = 0;
            this.items          = [];
          }, 2500);
        },

        error: (err: any) => {
          console.log(err);
          this.toastr.error('Error al crear la venta');
        },

        complete: () => {}
      });     
    
    }


  eliminarProducto(item: any) {
    this.items = this.items.filter(p => p !== item);
  }

  subtotal:any  = 0;
  descuentoTotal:any = 0;
  iva0:any      = 0;
  iva:any     = 0;
  total:any     = 0;
  
  calcularTotales() {
    let subtotal = 0;
    let descuento = 0;
    let iva0 = 0;
    let iva = 0;

    this.items.forEach(p => {
      const price = Number(p.price) || 0;
      const quantity = Number(p.quantity) || 0;
      const discount = Number(p.discount) || 0;
      const tarifa = Number(p.iva) || 0;
      const base = (price * quantity) - discount;

      subtotal += price * quantity;
      descuento += discount;

      if (tarifa === 0) {
        iva0 += base;
      } else {
        iva += base * 0.15;
      }
    });

    this.subtotal = subtotal;
    this.descuentoTotal = descuento;
    this.iva0 = iva0;
    this.iva = iva;
    this.total = (subtotal - descuento) + iva;
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
