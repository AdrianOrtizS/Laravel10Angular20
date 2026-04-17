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
  // type_receivable:any,
  form_pay:     any,
  iva0:         any,
  iva:          any,
  total:        any,
  plazo:        any,
  unidadTiempo: any,
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
  styleUrl: './create.component.scss',
  host: {
    'class': 'example',
  },
})
export class CreateComponent {

  public favoriteColor = '#26ab3c';
  icons   = freeSet;
  router  = inject(Router);
  toastr  = inject(ToastrService);
  
  customerService = inject(CustomerService);
  productService  = inject(ProductService);
  saleService     = inject(SaleService);
  plazo:any = 0;
  unidadTiempo: any = 'dias';
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

  // selectedTypeSale: string = '1'; // Valor por defecto
  // Opcional: función para hacer algo cuando cambia
  // onTypeSaleChange() {
  //   // console.log('Tipo de compra seleccionado:', this.selectedTypeBuy);
  // }
  form_pay:string = '';

  formPays:any = [
    {id:1, code: '01', description:'SIN UTILIZACION DEL SISTEMA FINANCIERO'},
    {id:2, code: '15', description:'COMPENSACIÓN DE DEUDAS'},
    {id:3, code: '16', description:'TARJETA DE DÉBITO'},
    {id:4, code: '17', description:'DINERO ELECTRÓNICO'},
    {id:5, code: '18', description:'TARJETA PREPAGO'},
    {id:6, code: '19', description:'TARJETA DE CRÉDITO'},
    {id:7, code: '20', description:'OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO'},
    {id:8, code: '21', description:'ENDOSO DE TÍTULOS'}
  ];

  // SIN UTILIZACION DEL SISTEMA FINANCIERO  01 contado
  // COMPENSACIÓN 4DE DEUDAS                 15 credito  
  // TARJETA DE DÉBITO                       16 contado
  // DINERO ELECTRÓNICO                      17 contado
  // TARJETA PREPAGO                         18 contado
  // TARJETA DE CRÉDITO                      19 contado
  // OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO  20 credito
  // ENDOSO DE TÍTULOS                       21 credito

  see_days_for_pay:boolean = false;
  select_form_pay(item:any){
    if(item == 20 || item == 21 || item == 15){
      this.see_days_for_pay = true;
    }else{
      this.see_days_for_pay = false;
    }
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
      // console.log(resp.Products.data);
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
    // console.log(this.ivaValor.value); //15.00

    var iva = 0; 
    if(this.productSelect.tarifa_iva == 1){
          iva = 15;
    }else{
      iva = 0;
    }

    this.productSelect = product;
    this.producto.set({ id_product: this.productSelect.id,
                        cod_pro:    this.productSelect.cod_pro, 
                        name:       this.productSelect.name, 
                        quantity:   1, 
                        price:      this.productSelect.price, 
                        discount:   0,
                        iva: iva 
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
  updatePlazo(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.plazo = valor;
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

  btnSaveBlock:boolean = false;

  isSaleValid(){
    return (
      this.customerSelect.id > 0 &&
      this.items.length > 0 &&
      this.form_pay != '' 
      && this.btnSaveBlock == false
    );
  }
  


  sale: SaleI = {
    id_customer:  0,
    subtotal:     0,
    discount:     0,
    iva0:         0,
    iva:          0,
    total:        0,
    // type_receivable : 1,
    form_pay:     '',
    plazo:        0,
    unidadTiempo: 'dias',
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


  increaseQuantity() {
    const current = this.producto().quantity || 0;
    this.producto().quantity = current + 1;
  }
  decreaseQuantity() {
    const current = this.producto().quantity || 0;
    if (current > 1) {
      this.producto().quantity = current - 1;
    }
  }
  increaseDiscount() {
    const current = this.producto().discount || 0;
    this.producto().discount = current + 1;
  }
  decreaseDiscount() {
    const current = this.producto().discount || 0;
    if (current > 1) {
      this.producto().discount = current - 1;
    }
  }

  increaseQuantityDay() {
    const current = this.plazo || 0;
    this.plazo = current + 1;

  }
  decreaseQuantityDay() {
    const current = this.plazo || 0;
    if (current > 1) {
      this.plazo = current - 1;
    }
  }



  baseImponible:any = 0;
  agregarProducto() {
    
    this.producto().subtotal = this.producto().price * this.producto().quantity;
    this.producto().discount = ((this.producto().discount * this.producto().price)/100)*this.producto().quantity;
    this.producto().iva = Number((((this.producto().subtotal-this.producto().discount)*this.producto().iva)/100).toFixed(2));
    
    this.baseImponible = this.producto().subtotal - this.producto().discount;

    // console.log(this.producto());
    if(this.ivaValor.value == '15.00'){
      // console.log('15.00');
      this.producto().impuesto = { 'codigo'    : '2',   //  2-iva, 3-ice, 5-IRBPNR
                                   'codigoPorcentaje' : '4', //0   0%
                                                             //2   12% (antiguo)
                                                             //4   15% (actual Ecuador)
                                                             //6   No objeto
                                                             //7   Exento
                                   'tarifa'           : this.ivaValor.value,  // desde BD
                                   'baseImponible'    : this.baseImponible,
                                   'valor': this.producto().iva.toFixed(2) };
    }
    if(this.ivaValor.value == '14.00'){
      // console.log('14.00');
      this.producto().impuesto = { 'codigo'    : '2',   //  2-iva, 3-ice, 5-IRBPNR
                                   'codigoPorcentaje' : '3', //0   0%
                                                             //2   12% (antiguo)
                                                             //4   15% (actual Ecuador)
                                                             //6   No objeto
                                                             //7   Exento
                                   'tarifa'           : this.ivaValor.value,  // desde BD
                                   'baseImponible'    : this.baseImponible,
                                   'valor': this.producto().iva.toFixed(2) };
    }
    if(this.ivaValor.value == '12.00'){
      // console.log('12.00');
      this.producto().impuesto = { 'codigo'    : '2',   //  2-iva, 3-ice, 5-IRBPNR
                                   'codigoPorcentaje' : '2', //0   0%
                                                             //2   12% (antiguo)
                                                             //4   15% (actual Ecuador)
                                                             //6   No objeto
                                                             //7   Exento
                                   'tarifa'           : this.ivaValor.value,  // desde BD
                                   'baseImponible'    : this.baseImponible,
                                   'valor': this.producto().iva.toFixed(2) };
    }
    if(this.ivaValor.value == '0.00'){
      // console.log('0.00');
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
    this.btnSaveBlock = true;
    this.sale = {
      id_customer:    this.customerSelect.id,
      // type_receivable:  this.selectedTypeSale, //1 contado - 2 credito
                                      // SIN UTILIZACION DEL SISTEMA FINANCIERO     01 contado  
                                      // COMPENSACIÓN DE DEUDAS                     15 credito
                                      // TARJETA DE DÉBITO                          16 contado
      form_pay:     this.form_pay,    // DINERO ELECTRÓNICO                         17 contado
                                      // TARJETA PREPAGO                            18 contado
                                      // TARJETA DE CRÉDITO                         19 contado
                                      // OTROS CON UTILIZACIÓN DEL SISTEMA FINANCIERO  20  credito
                                      // ENDOSO DE TÍTULOS                          21  credito
      subtotal:     this.subtotal,
      discount:     this.descuentoTotal,
      iva0:         this.iva0,
      iva:          this.iva,
      plazo:        this.plazo,
      unidadTiempo: 'dias',
      total:        this.total,
      items:        this.items
    };
    // console.log(this.sale);

    this.saleService.createSale(this.sale)
      .subscribe({
        next: (resp: any) => {
          this.toastr.success('Éxito', 'La venta se ha creado correctamente');
          setTimeout(() => {
            this.printSale(resp.sale.id);
            this.customerSelect   = {};
            this.subtotal         = 0;
            this.total            = 0;
            this.iva0             = 0;
            this.iva              = 0;
            this.descuentoTotal   = 0;
            this.items            = [];
            this.form_pay         = '';
            this.plazo            = 0; 
            this.see_days_for_pay = false;
            this.btnSaveBlock           = false;
          }, 2500);
        },
        error: (err: any) => {
          console.log(err);
          this.btnSaveBlock = false;
          this.toastr.error('Error al crear la venta');
        },
        complete: () => {
          this.btnSaveBlock = false;
        }
    });     
    
  }


  eliminarProducto(item: any) {
    this.items = this.items.filter(p => p !== item);
  }

  subtotal:any  = 0;
  descuentoTotal:any = 0;
  iva0:any      = 0;
  iva:any       = 0;
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
        // console.log(this.ivaValor);
        iva += base * (+(this.ivaValor.value)/100);
        // console.log(iva);
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
