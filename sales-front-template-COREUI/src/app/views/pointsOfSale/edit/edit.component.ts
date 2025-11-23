import { PointsOfSaleService } from './../points_of_sale.service';
import { SharedModule } from './../../../shared/shared.module';
import { ToastrService } from 'ngx-toastr';
import { ActivatedRoute, Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { Component, computed, effect, inject, signal } from '@angular/core';
import { ReactiveFormsModule } from '@angular/forms';
import { FormSelectDirective } from '@coreui/angular';

interface PointsOfSaleI {
  id_branch: number,
  codigo_punto_emision: string,
  secuencial_actual: number,
  descripcion: string,
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

    public favoriteColor = '#26ab3c';
    icons   = freeSet;
    router  = inject(Router);
    toastr  = inject(ToastrService);
    pointsOfSaleService = inject(PointsOfSaleService);
    activatedRoute = inject(ActivatedRoute);

    /**
     *
     */
    // constructor() {
    //   effect(() => {
    //     console.log('Producto:', this.PRODUCT());
    //   });
    // }
    
    POINTSOFSALE:any = signal<PointsOfSaleI>({
      id_branch: 0,
      codigo_punto_emision: '',
      secuencial_actual: 0,
      descripcion: '',
    });

    POINTSOFSALE_ID:any  = null;
    Branches:any = [];
    isEmpty:boolean = true;
    
    ngOnInit(){
      this.pointsOfSaleService.getBranches()
      .subscribe((resp:any)=>{
        this.Branches = resp.Branches;
        console.log(this.Branches);
      });
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.POINTSOFSALE_ID = resp.id;
      });
      this.pointsOfSaleService.showPointsOfSale(this.POINTSOFSALE_ID)
      .subscribe((resp:any)=>{
                
        this.isEmpty = Object.keys(this.POINTSOFSALE()).length === 0;
        this.POINTSOFSALE.set(resp.pointsOfSale);
        console.log(this.POINTSOFSALE());
      });
    }

    // Métodos para update cada campo (evita parser error)
    updateId_branch(value: number) {
      this.POINTSOFSALE.update((c:any) => ({ ...c, id_branch: +value }));
    }

    updateCodigoPuntoEmision(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, codigo_punto_emision: valor }));
    }

    updateSecuencialActual(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, secuencial_actual: valor }));
    }

    updateDescripcion(event: Event) {
      const valor = (event.target as HTMLInputElement).value;
      this.POINTSOFSALE.update((c:any) => ({ ...c, descripcion: valor }));
    }

    // Validar si todos los campos son obligatorios y válidos
    isFormValid = computed(() => {
      const c = this.POINTSOFSALE();
      return (
        c.id_branch > 0 &&
        c.codigo_punto_emision.trim().length > 0 && 
        c.secuencial_actual >= 0 &&
        c.descripcion.trim().length > 0
      );
    });

    save(){


      if(this.POINTSOFSALE().id_branch == 0){
        this.toastr.error('Validacion', 'Seleccione sucursal');
        return;
      }

      let point = {
        'id_branch': this.POINTSOFSALE().id_branch,
        'codigo_punto_emision': this.POINTSOFSALE().codigo_punto_emision,
        'secuencial_actual': this.POINTSOFSALE().secuencial_actual,
        'descripcion':this.POINTSOFSALE().descripcion
      };

      this.pointsOfSaleService.updatePointsOfSale(this.POINTSOFSALE_ID, point)
        .subscribe({
          next: (resp:any) =>{
            console.log(resp);
            this.toastr.success('Exito', 'El punto de venta se ha actualizado correctamente');
          },
          error: (error:any)=>{
            console.log(error.error.errors);
            this.toastr.error('Error', error.error.message);

          }
        });
    }

    goList(){
      this.router.navigateByUrl("/pointsOfSale/list");
    }

}
