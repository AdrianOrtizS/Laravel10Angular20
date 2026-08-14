import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';
import { Component, computed, inject, signal } from '@angular/core';
import { SharedModule } from '../../../shared/shared.module';
import { Router } from '@angular/router';
import { SaleService } from '../sale.service';
import { ButtonDirective, FormCheckComponent, FormCheckInputDirective, FormSelectDirective, ListGroupDirective, ListGroupItemDirective, ModalBodyComponent, ModalComponent, ModalHeaderComponent, ModalTitleDirective, ModalToggleDirective } from '@coreui/angular';
import { CustomerService } from '../../customer/customer.service';
import { ProductService } from '../../inventory/product/product.service';

//para crear cliente desde la venta
interface CustomerI {
  name:   string,
  email:  string,
  phone:  string,
  num_identificador:string,
  address: string,
  tipo_identificador:string
}

interface SaleI {
  id_customer:  any,
  subtotal:     any,
  discount:     any,
  form_pay:     any,
  iva0:         any,
  iva:          any,
  ice:          any,
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
  imports: [SharedModule, FormSelectDirective, ListGroupDirective, ListGroupItemDirective, ButtonDirective, ModalToggleDirective, ModalComponent, ModalHeaderComponent, ModalTitleDirective, ModalBodyComponent, FormCheckComponent, FormCheckInputDirective],
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
    phone:  '',
    tipo_identificador: ''
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
      console.log(resp);
      this.products.set(resp.Products.data);
      this.searchProductNull = true;
    });
  }

  searchToProductKey(){
    if(this.search_ngModelProduct.length > 5){
      this.cargarProducts(this.search_ngModelProduct);
      this.search_ngModelProduct = '';
    }
  }
  
  //trae products de acuerdo al criterio de busqueda
  searchToProduct(){    
    console.log(this.search_ngModelProduct);
    // if(this.search_ngModelProduct.length > 5){
      this.cargarProducts(this.search_ngModelProduct);
      this.search_ngModelProduct = '';
    // }
  }
  // searchToProductLectorbarras(){    
  //   this.cargarProducts(this.search_ngModelProduct);
  //   this.search_ngModelProduct = '';
  // }
  selectProduct(product:any){
    // console.log(product);
    var iva = 0; 
    var ice = 0;
    if(product.tarifa_iva == 1 || product.tarifa_iva == '1'){
      iva = this.ivaValor.value;
    }else{
      iva = 0;
    }
    if(product.id_ice_tarifa != null && product.tarifa_ice != null){
      ice = product.tarifa_ice.tarifa;
    }else{
      ice = 0;
    }
    this.productSelect = product;
    this.producto.set({ id_product:   this.productSelect.id,
                        cod_pro:      this.productSelect.cod_pro, 
                        name:         this.productSelect.name, 
                        quantity:     1, 
                        price:        this.productSelect.price, 
                        discount:     0,
                        iva:          iva,
                        ice:          ice,
                        tarifa_ice:   this.productSelect.tarifa_ice
                      });
    // console.log(this.producto());
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

  nombreTouched = false;
  updateTipo_identificador(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.customerNew.update((c:any) => ({ ...c, tipo_identificador: valor }));
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
      c.tipo_identificador.trim().length > 0 &&
      this.isEmailValid()
    );
  });

  visibleModalCreateCustom = false;
  saveCustomer(){
    this.customerService.createCustomer(this.customerNew())
    .subscribe((resp:any) =>{
      console.log(resp);
      // this.toastr.error('Validacion', 'El cliente ya existe');
      // return;
      if(resp.code == 403){
        let errors = Object.values(resp.message);
        errors.forEach((err:any) => {
          setTimeout(() => {
            this.toastr.error('Validacion', err);
          }, 800);
        });
        return;
      }
    
      this.customerSelect = resp.customer;
      this.customers.set([]);
      this.search_ngModelCustomer = '';

      if(resp.code == 200){
        this.customerNew().name = '';
        this.customerNew().surname = '';
        this.customerNew().num_identificador = '';
        this.customerNew().email = '';
        this.customerNew().phone = '';
        this.customerNew().address = '';

        this.toastr.success('Exito', 'El cliente se ha creado correctamente');
        this.visibleModalCreateCustom = false;
        return;
      }
    });
  }

  closeModalCreateCustom(){
    this.visibleModalCreateCustom = false;
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
  updateIce(event: Event) {
    const valor = (event.target as HTMLInputElement).value;
    this.producto.update((c:any) => ({ ...c, ice: valor }));
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
    ice:          0,
    total:        0,
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
    ice:        0,
    tarifa_ice: {},
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
    this.producto().subtotal =
        this.producto().price * this.producto().quantity;
    // descuento en dinero
    this.producto().discount =
        ((this.producto().discount * this.producto().price) / 100)
          * this.producto().quantity;
    // base sin impuestos
    this.baseImponible =
        this.producto().subtotal - this.producto().discount;
    // ICE
    this.producto().ice = Number(
        (
          (this.baseImponible * this.producto().ice) / 100
        ).toFixed(2)
    );
    // Base IVA (SRI)
    const baseImponibleIva =
    this.baseImponible + this.producto().ice;
    // IVA
    this.producto().iva = Number(
        (
          (baseImponibleIva * this.producto().iva) / 100
        ).toFixed(2)
    );
    // ================= IVA =================
    let codigoPorcentajeIva = '0';
    let tarifaIva = '0';
    if (this.producto().iva > 0) {
        tarifaIva = this.ivaValor.value;
        if (this.ivaValor.value === '12.00') {
            codigoPorcentajeIva = '2';
        }
        if (this.ivaValor.value === '14.00') {
            codigoPorcentajeIva = '3';
        }
        if (this.ivaValor.value === '15.00') {
            codigoPorcentajeIva = '4';
        }
    }
    this.producto().impuesto = {
        codigo: '2',
        codigoPorcentaje: codigoPorcentajeIva,
        tarifa: tarifaIva,
        baseImponible: Number(baseImponibleIva.toFixed(2)),
        valor: this.producto().iva.toFixed(2)
    };
    // ================= ICE =================
    if (this.producto().ice > 0) {
        this.producto().impuesto_ice = {
            codigo: '3',
            codigoPorcentaje: this.producto().tarifa_ice.codigo_porcentaje,
            tarifa: this.producto().tarifa_ice.tarifa,
            baseImponible: this.baseImponible.toFixed(2),
            valor: this.producto().ice.toFixed(2)
        };

    } else {
        this.producto().impuesto_ice = null;
    }

    this.items.push({ ...this.producto() });

    this.producto.set({
        id_product: '',
        cod_pro: '',
        name: '',
        quantity: 1,
        price: 0,
        subtotal: 0,
        iva: 0,
        ice: 0,
        tarifa_ice: {},
        discount: 0
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
      ice:          this.ice,
      plazo:        this.plazo,
      unidadTiempo: 'dias',
      total:        this.total,
      items:        this.items
    };

    this.saleService.createSale(this.sale)
    .subscribe({
      next: (resp: any) => {
        if(resp.resp.code != 500 || resp.resp.code != '500'){
          this.toastr.success('Éxito', 'La venta se ha creado correctamente');
          
          
          setTimeout(() => {
            // console.log(resp);
            this.customerSelect   = {};
            this.subtotal         = 0;
            this.total            = 0;
            this.iva0             = 0;
            this.iva              = 0;
            this.ice              = 0;
            this.descuentoTotal   = 0;
            this.items            = [];
            this.form_pay         = '';
            this.plazo            = 0; 
            this.see_days_for_pay = false;
            this.btnSaveBlock     = false;
          }, 1000);


          const interval = setInterval(() => {
            this.saleService.reconsultarSri(resp.sale.id)
              .subscribe({
                next: (respuesta: any) => {
                  // console.log(respuesta);
                  if (!respuesta) {
                    return;
                  }
                  // console.log(respuesta.estado);
                  if (respuesta.estado === 'AUTORIZADO') {

                    clearInterval(interval); // ⛔ Detiene el setInterval

                    this.toastr.success(
                      'La factura ya fue autorizada.',
                      'Éxito'
                    );
                    setTimeout(() => {
                      this.printSale(resp.sale.id);
                    }, 2500);
                  }
                },
                error: (err: any) => {
                  console.log(err);
                }
              });
          }, 1000);


        }else{
          this.btnSaveBlock = false;
          this.toastr.error('Error al crear la venta');
        }
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
    this.calcularTotales();
  }

  
  subtotal:any  = 0;
  descuentoTotal:any = 0;
  iva0:any      = 0;
  iva:any       = 0;
  ice:any       = 0;
  total:any     = 0;

  calcularTotales() {
      let subtotal = 0;
      let descuento = 0;
      let iva0 = 0;
      let iva = 0;
      let ice = 0;

      this.items.forEach(p => {
          subtotal += Number(p.subtotal) || 0;
          descuento += Number(p.discount) || 0;
          iva += Number(p.impuesto?.valor) || 0;
          ice += Number(p.impuesto_ice?.valor) || 0;
          if ((Number(p.impuesto?.tarifa) || 0) === 0) {
              iva0 += Number(p.impuesto?.baseImponible) || 0;
          }
      });

      this.subtotal = Number(subtotal.toFixed(2));
      this.descuentoTotal = Number(descuento.toFixed(2));
      this.iva0 = Number(iva0.toFixed(2));
      this.iva = Number(iva.toFixed(2));
      this.ice = Number(ice.toFixed(2));
      this.total = Number(
          (
              (this.subtotal - this.descuentoTotal)
              + this.iva
              + this.ice
          ).toFixed(2)
      );
  }

  printSale(id_sale: any) {
    this.saleService.getFacturaPDF(id_sale)
      .subscribe({
        next: (pdfBlob: Blob) => {
          const url = URL.createObjectURL(pdfBlob);
          const printWindow = window.open('', '_blank');
          if (printWindow) {
            printWindow.location.href = url;
            printWindow.onload = () => {
              printWindow.focus();
              printWindow.print();
            };
          }
        },
        error: (err) => {
          console.error(err);
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
