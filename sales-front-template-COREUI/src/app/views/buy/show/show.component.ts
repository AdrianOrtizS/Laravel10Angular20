import { CommonModule } from '@angular/common';
import { Component, inject, signal } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { ButtonDirective, CardModule,  ModalModule,  SharedModule, TableModule } from '@coreui/angular';
import moment from 'moment';
import { freeSet } from '@coreui/icons';
import { IconDirective } from '@coreui/icons-angular';
import { BuyService } from '../buy.service';

@Component({
  selector: 'app-show',
  imports: [SharedModule,ModalModule ,ButtonDirective , CommonModule, CardModule, TableModule, IconDirective ],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss'
})
export class ShowComponent {

  /** Íconos de CoreUI */
  icons = freeSet;
  /** Inyección de dependencias */
  private activatedRoute = inject(ActivatedRoute);
  private buyService = inject(BuyService);
  private router = inject(Router);

  /** Variables de estado */
  BUY_ID: any;
  BUY = signal<any>({});
  tiempo_creacion: string | null = null;
  isLoading = signal<boolean>(true);

  /** Modal de imagen */
  showModal = false;
  selectedPay: any = null;


  ngOnInit() {
    // Obtener ID de la ruta
    this.activatedRoute.params.subscribe((resp: any) => {
      this.BUY_ID = resp.id;
      if (this.BUY_ID) this.loadBuy();
    });
  }
  
  /** Cargar información de la compra */
  private loadBuy() {
    this.isLoading.set(true);
    this.buyService.showBuy(this.BUY_ID).subscribe({
      next: (resp: any) => {
        this.BUY.set(resp);
        this.isLoading.set(false);
      },
      error: (err) => {
        // console.error('Error al cargar la factura:', err);
        this.isLoading.set(false);
      }
    });
  }

  showDeleteModal = false;
  payToDelete: any = null;

  // Abre el modal con el pago seleccionado
  confirmDelete(pay: any) {
    this.payToDelete = pay;
    this.showDeleteModal = true;
  }

  // Elimina el pago confirmado
  deletePayConfirmed() {
    if (!this.payToDelete) return;

    this.buyService.deletePay(this.payToDelete.id).subscribe({
      next: (resp: any) => {
        console.log('Pago eliminado correctamente:', resp);
        // Actualizamos la lista de pagos y recalculamos totales
        const updatedPays = this.BUY().pays.filter((p: any) => p.id !== this.payToDelete.id);
        this.BUY.update((current: any) => ({
          ...current,
          pays: updatedPays,
          total_abonos: updatedPays.reduce((sum: number, p: any) => sum + p.valor_abono, 0),
          saldo: current.total - updatedPays.reduce((sum: number, p: any) => sum + p.valor_abono, 0)
        }));
        this.showDeleteModal = false;
        this.payToDelete = null;
      },
      error: (err: any) => {
        // console.error('Error al eliminar el pago:', err);
        this.showDeleteModal = false;
      }
    });
  }

  /** Mostrar modal de imagen */
  openModal(pay: any) {
    this.selectedPay = pay;
    this.showModal = true;
  }

  /** Regresar a la lista */
  goList() {
    this.router.navigateByUrl('/buy/list');
  }
}
