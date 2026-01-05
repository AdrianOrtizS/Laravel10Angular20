import { Component, inject, signal } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ToastrService } from 'ngx-toastr';
import { Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { ReactiveFormsModule, FormBuilder, Validators } from '@angular/forms';
import { ModalModule } from '@coreui/angular';
import { SharedModule } from '../../../shared/shared.module';
import { AuthService } from '../../services/auth.service';
import { URL_BACKEND } from '../../../config/config';

@Component({
  selector: 'app-show',
  imports: [CommonModule, SharedModule, ReactiveFormsModule, ModalModule],
  templateUrl: './show.component.html',
  styleUrl: './show.component.scss'
})
export class ShowComponent {

  icons = freeSet;
  toastr = inject(ToastrService);
  router = inject(Router);
  authService = inject(AuthService);
  fb = inject(FormBuilder);
  // imagen:string = '';
  imagen_previsualiza:any = '../../../../assets/images/sin_imagen.jpg';
  file_imagen:any =null;

  user = signal<any>({
    branch: {},
    email: '',
    imagen:'',
    name: '',
    point_of_sale: '',
    // role: '',
  });

  showEditModal = false;

  profileForm = this.fb.group({
    name: ['', [Validators.required]],
    imagen: ['', []]
    // email: ['', [Validators.required, Validators.email]],
  });

  passwordForm = this.fb.group({
    // current_password: ['', [Validators.required]],
    new_password:     ['', [Validators.required, Validators.minLength(6)]],
    confirm_password: ['', [Validators.required]],
  });

  ngOnInit() {
    // console.log(this.authService.user());
    this.user.set(this.authService.user());
    this.profileForm.patchValue({
      name: this.user().name,
    });

    if(this.user().imagen){
      let url = this.user().imagen;
      this.imagen_previsualiza = url;
      // console.log(this.imagen_previsualiza);
    }
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

  openEdit() {
    this.showEditModal = true;
  }

  closeEdit() {
    this.showEditModal = false;
  }

  saveProfile() {
    if (this.profileForm.invalid) return;
    
    let formData = new FormData();
    formData.append('name', this.profileForm.value.name?.toString() ?? '');

    if(this.file_imagen){
      formData.append('imagen', this.file_imagen);
    }

    this.authService.updateUserLog(formData).subscribe({
      next: (resp:any) => {
        this.user().name = this.profileForm.value.name;
        if (resp.user?.imagen) {
          this.user().imagen = resp.user.imagen;
        }
        localStorage.setItem('user', JSON.stringify(this.user()));
        this.authService.updateUser(resp.user);
        this.toastr.success('Perfil actualizado');
        // console.log(this.authService.user());
        this.showEditModal = false;
      },
      error: (err:any) =>{
        console.log(err);
        this.toastr.error('Error al actualizar')
      } 
    });
  }

  changePassword() {
    const {new_password, confirm_password } = this.passwordForm.value;
    if (new_password !== confirm_password) {
      this.toastr.error('Las contraseñas no coinciden');
      return;
    }
    let passwords = {
      // current_password: current_password, 
      new_password: new_password
    };
    // console.log(passwords);

    this.authService.changePasswordUserLog(passwords).subscribe({
      next: () => {
        this.toastr.success('Contraseña actualizada');
        this.passwordForm.reset();
      },
      error: () => this.toastr.error('Error al cambiar contraseña')
    });
  }

}
