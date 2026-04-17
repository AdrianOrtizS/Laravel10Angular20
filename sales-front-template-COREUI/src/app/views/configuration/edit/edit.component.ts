import { ActivatedRoute } from '@angular/router';
import { Component, inject } from '@angular/core';
import { ConfigurationService } from '../configuration.service';
import { SharedModule } from './../../../shared/shared.module';
import { URL_BACKEND } from '../../../config/config';
import { Router } from '@angular/router';
import { ToastrService } from 'ngx-toastr';
import { Location } from '@angular/common';
import { freeSet } from '@coreui/icons';

@Component({
  selector: 'app-edit',
  imports: [ SharedModule ],
  templateUrl: './edit.component.html',
  styleUrl: './edit.component.scss',
  host: {
    'class': 'example',
  },
})
export class EditComponent {


    public favoriteColor = '#26ab3c';
    icons = freeSet;
    location = inject(Location); 
    toastr  = inject(ToastrService);
    router = inject(Router);
    configurationService = inject(ConfigurationService);
    activatedRoute = inject(ActivatedRoute);
    CONFIGURATION_ID:any;
    CONFIGURATION:any = {

    };
    name:string = '';
    value:string = '';
    state:boolean = true;
    
    tarifas_iva:any =[];

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.CONFIGURATION_ID = resp.id;
      });
      this.configurationService.showConfiguration(this.CONFIGURATION_ID)
      .subscribe((resp:any)=>{
        this.CONFIGURATION = resp.configuration;
        this.name = this.CONFIGURATION.name;
        this.value = this.CONFIGURATION.value;
        this.state = this.CONFIGURATION.state;
      });
      this.configurationService.getTarifasIva()
      .subscribe((resp:any)=>{
        this.tarifas_iva = resp.Tarifas_iva;
        console.log(this.tarifas_iva);
      });
    }


    save(){
      if(!this.name || !this.value){
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }

      this.CONFIGURATION.name = this.name;
      this.CONFIGURATION.value = this.value;

      this.configurationService.updateConfiguration(this.CONFIGURATION_ID, this.CONFIGURATION)
      .subscribe((resp:any) =>{
        if(resp.message == 403){
          this.toastr.error('Validacion', 'La configuracion ya existe');
          return;
        }
        this.toastr.success('Exito', 'La configuracion se actualizo correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/configuration/list");
    }
}
