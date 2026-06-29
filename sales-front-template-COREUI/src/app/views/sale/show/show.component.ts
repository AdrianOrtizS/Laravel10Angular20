import { CommonModule, Location } from '@angular/common';
import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ButtonDirective, CardModule,  ModalModule,  SharedModule, TableModule } from '@coreui/angular';
import moment from 'moment';
import { SaleService } from '../sale.service';
import { freeSet } from '@coreui/icons';
import { IconDirective } from '@coreui/icons-angular';

@Component({
  selector: 'app-show',
  imports: [SharedModule,ButtonDirective , CommonModule, CardModule, TableModule, IconDirective, ModalModule],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss'
})
export class ShowComponent {

  icons = freeSet;

  favoriteColor = '#26ab3c';

  activatedRoute = inject(ActivatedRoute);
  saleService = inject(SaleService);
  location = inject(Location);
  router = inject(Router);
  
  SALE_ID:any;
  SALE:any = signal<any>({});
  tiempo_creacion:any;
  isLoading = signal<boolean>(true);

    /** Modal de imagen */
  showModal = false;
  selectedReceivable: any = null;

  ngOnInit(){
    // Obtener ID de la ruta
    this.activatedRoute.params.subscribe((resp:any)=>{
      this.SALE_ID = resp.id;
      if (this.SALE_ID) this.loadSale();
    });

  }

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

  formPay:any ='';

  /** Cargar información de la compra */
  private loadSale() {
    this.isLoading.set(true);
    this.saleService.showSale(this.SALE_ID).subscribe({
      next: (resp: any) => {
        this.SALE.set(resp);
        console.log(this.SALE());
          this.formPays.forEach((element:any) => {
            if(element.code == this.SALE().form_pay){
              this.formPay = element.description;
            }
          });
        this.isLoading.set(false);
      },
      error: (err) => {
        this.isLoading.set(false);
      }
    });
  }

  showDeleteModal = false;
  receivableToDelete: any = null;

  // Abre el modal con el pago seleccionado
  confirmDelete(receivable: any) {
    this.receivableToDelete = receivable;
    this.showDeleteModal = true;
  }


  deleteReceivableConfirmed() {
    if (!this.receivableToDelete) return;

    this.saleService.deleteReceivable(this.receivableToDelete.id).subscribe({
      next: (resp: any) => {
        const updatedPays = this.SALE().receivables.filter((p: any) => p.id !== this.receivableToDelete.id);
        this.SALE.update((current: any) => ({
          ...current,
          receivables: updatedPays,
          total_abonos: updatedPays.reduce((sum: number, p: any) => sum + p.valor_abono, 0),
          saldo: current.infoFactura.importeTotal - updatedPays.reduce((sum: number, p: any) => sum + p.valor_abono, 0)
        }));
        this.showDeleteModal = false;
        this.receivableToDelete = null;
      },
      error: (err: any) => {
        this.showDeleteModal = false;
      }
    });
  }


  rePrintPdf(clave: any) {
    this.saleService.rePrintFacturaPDF(clave)
      .subscribe(
        (pdfBlob: Blob) => {
          const url = window.URL.createObjectURL(pdfBlob);
          const newWindow = window.open(url, '_blank');

          if (newWindow) {
            newWindow.onload = () => {
              newWindow.print();
            };
          }
        },
        (error) => {
          console.error('Error al obtener el PDF:', error);
        }
      );
  }

  /** Mostrar modal de imagen */
  openModal(receivable: any) {
    this.selectedReceivable = receivable;
    this.showModal = true;
  }

  printReceivable(receivable:any){
    this.saleService.rePrintPDF(receivable.id).subscribe((pdfBlob: Blob) => {
        const url = window.URL.createObjectURL(pdfBlob);
        const newWindow = window.open(url, '_blank');
        if (newWindow) {
          newWindow.print(); // abre el diálogo de impresión directamente
        }
    });
  }
  
  goList(){
    this.router.navigateByUrl("/sale/list");
  }

}
