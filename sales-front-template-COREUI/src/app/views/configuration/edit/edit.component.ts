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

    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    file_imagen:any =null;

    ngOnInit(){
      this.activatedRoute.params.subscribe((resp:any)=>{
        this.CONFIGURATION_ID = resp.id;
      });
      this.configurationService.showConfiguration(this.CONFIGURATION_ID)
      .subscribe((resp:any)=>{
        this.CONFIGURATION = resp.configuration;

        this.name = this.CONFIGURATION.name;
        this.value = this.CONFIGURATION.value;
        if (this.name === 'logoPdf') {
          this.imagen_previsualiza = this.value;
        }

        console.log(this.CONFIGURATION);
        this.state = this.CONFIGURATION.state;
      });
      this.configurationService.getTarifasIva()
      .subscribe((resp:any)=>{
        this.tarifas_iva = resp.Tarifas_iva;
      });
    }

    
    clickInputFileHide(){
      const clickInputFile = document.getElementById('logoPdf');
      clickInputFile?.click();
    }

    processFile($event:any){
      this.file_imagen = null;
      if($event.target.files[0].type.indexOf('image') < 0){
        return;
      }
      this.file_imagen = $event.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(this.file_imagen);
      reader.onloadend = ()=> this.imagen_previsualiza = reader.result;
    }

    save(){
      if (!this.name || (!this.value && !this.file_imagen)) {
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }
      let formData = new FormData();
      formData.append('name',  this.name);
      
      if(this.file_imagen){
        formData.append('file_imagen', this.file_imagen);
      }else{
        formData.append('value', this.value);
      }

      this.configurationService.updateConfiguration(this.CONFIGURATION_ID, formData)
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
