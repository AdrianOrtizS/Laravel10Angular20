// import { URL_BACKEND } from './../../../config/config';
import { ActivatedRoute, Router } from '@angular/router';
import { Component, inject } from '@angular/core';
import { CategorieService } from '../categorie.service';
import { SharedModule } from './../../../shared/shared.module';
import { ToastrService } from 'ngx-toastr';
import { freeSet } from '@coreui/icons';

@Component({
  selector: 'app-create',
  imports: [  SharedModule ,  ],
  templateUrl: './create.component.html',
  styleUrl: './create.component.scss',
  host: {
    'class': 'example',
  },
})
export class CreateComponent {

    public favoriteColor = '#26ab3c';
    icons = freeSet;
    router = inject(Router);
    toastr  = inject(ToastrService);
    categorieService = inject(CategorieService);
    activatedRoute = inject(ActivatedRoute);
    // CATEGORIE_ID:any;
    CATEGORIE:any;
    name:string = '';
    description:string = '';
    imagen:string = '';
    state:boolean = true;
    imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
    file_imagen:any =null;
    
    ngOnInit(){
    
    }

    clickInputFileHide(){
      const clickInputFile = document.getElementById('categorieImage');
      clickInputFile?.click();
    }

    processFile($event:any){
      if($event.target.files[0].type.indexOf('image') < 0){
        return;
      }
      this.file_imagen = $event.target.files[0];
      let reader = new FileReader();
      reader.readAsDataURL(this.file_imagen);
      reader.onloadend = ()=> this.imagen_previsualiza = reader.result;
    }


    save(){
      if(!this.name || !this.description){
        this.toastr.error('Validacion', 'Los campos con * son obligatorios');
        return;
      }

      let formData = new FormData();
      formData.append('name', this.name);
      formData.append('description', this.description);

      if(this.file_imagen){
        formData.append('categorie', this.file_imagen);
      }

      this.categorieService.createCategorie(formData)
      .subscribe((resp:any) =>{
        // console.log(resp);
        if(resp.code == 403){
          this.toastr.error('Validacion', 'La categorie ya existe');
          return;
        }
        this.name = '';
        this.description = '';
        this.file_imagen = null;
        this.imagen_previsualiza = '../../../../assets/images/sin_imagen.jpg';        
        this.toastr.success('Exito', 'La categorie se ha creado correctamente');
      });
    }

    goList(){
      this.router.navigateByUrl("/categorie/list");
    }

}
