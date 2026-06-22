import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { IceTarifaService } from '../ice-tarifa.service';
import moment from 'moment';
// import 'moment/locale/es';
import { SharedModule } from '../../../../shared/shared.module';

@Component({
  selector: 'app-show',
  imports: [SharedModule],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss',
})
export class ShowComponent {

    // moment.locale('es');

    icons = freeSet;

    favoriteColor = '#26ab3c';
    router = inject(Router);
    iceTarifaService = inject(IceTarifaService);
    activatedRoute = inject(ActivatedRoute);

    ICE_TARIFA_ID:any;
    ICE_TARIFA:any;
    codigo:string = '';
    codigo_porcentaje:string = '';
    descripcion:string = '';
    tipo:string = '';
    tarifa:string = '';
    unidad:string = '';
    estado:string = '';

    tiempo_creacion:any;
    state:boolean = true;

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.ICE_TARIFA_ID = resp.id;
      });
      this.iceTarifaService.showIceTarifa(this.ICE_TARIFA_ID)
      .subscribe((resp:any)=>{
        
        console.log(resp.iceTarifa);

        this.ICE_TARIFA = resp.iceTarifa;
        this.codigo = this.ICE_TARIFA.codigo;
        this.codigo_porcentaje = this.ICE_TARIFA.codigo_porcentaje;
        this.descripcion = this.ICE_TARIFA.descripcion;
        this.tipo = this.ICE_TARIFA.tipo;
        this.tarifa = this.ICE_TARIFA.tarifa;
        this.unidad =this.ICE_TARIFA.unidad;
        this.estado = this.ICE_TARIFA.estado;
        this.tiempo_creacion = moment(this.ICE_TARIFA.created_at).fromNow();
      });
    }

    goBack(){
      this.router.navigateByUrl("/inventory/list-ice-tarifa");
    }

    goUpdate(){
      this.router.navigate(['/inventory/edit-ice-tarifa/'+this.ICE_TARIFA_ID]);
    }

    goList(){
      this.router.navigateByUrl("/inventory/list-ice-tarifa");
    }

  }    