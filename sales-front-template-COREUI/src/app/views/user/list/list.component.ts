import { Component, inject, signal } from '@angular/core';
import { Router } from '@angular/router';
import { freeSet } from '@coreui/icons';
import { ToastrService } from 'ngx-toastr';
import { UserService } from '../user.service';
import { SharedModule } from '../../../shared/shared.module';

@Component({
  selector: 'app-list',
  imports: [SharedModule],
  templateUrl: './list.component.html',
  styleUrl: './list.component.scss',
})
export class ListComponent {

  icons = freeSet;

  userService = inject(UserService);
  router  = inject(Router);
  toastr  = inject(ToastrService);

  users:   any = signal<any[]>([]);
  search:     string ='';
  totalPages: number =0;
  currentPage:number =1;
  pageSize:   number = 5;

  itemForPage:any = [
    {'id':1,'val':1},
    {'id':2,'val':2},
    {'id':5,'val':5},
    {'id':10,'val':10},
    {'id':20,'val':20}];

  selectedUser = signal<any | null>(null);

  changePage(page: number): void {
    if (page >= 1 && page <= this.totalPages) {
      this.currentPage = page;
    }
  }

  ngOnInit(){
    this.listarUsers();
  }

  refresh(){
    this.search = '';
    this.listarUsers(1);  
  }

  verifData = 0;
  listarUsers(page = 1){
    this.userService.listUsers(page, this.search, this.pageSize)
    .subscribe((resp:any) => {
      // console.log(resp);
      this.verifData = 0;
      if(resp.total == 0 && this.verifData == 0){
        this.verifData ++;
        this.toastr.warning('Sin datos', 'No hay informacion que coincida con el criterio de busqueda');
        return;
      }
      this.totalPages = resp.total;
      this.currentPage = page;
      console.log(resp);
      return this.users.set(resp.users) ;
    });
  }


  searchTo(){
    this.listarUsers();
  }

  loadPage($event:any){
    this.listarUsers($event);
  }
  
  abrirModal(id: number) {
    // console.log(id);
    this.selectedUser.set(id);
  }

  cerrarModal() {
    this.selectedUser.set(null);
  }

  changeState(user_id:any){
    this.userService.changeState(user_id)
    .subscribe((resp:any) => {
      // console.log(resp);
      let state = false;
      if(resp[1] === 'User deactivate'){
        state = false;
        this.toastr.success('Exito', 'usuario se ha desactivado correctamente');
      }else{
        state = true;
        this.toastr.success('Exito', 'usuario se ha activado correctamente');
      }
      this.actualizarUser(user_id, state);
      this.cerrarModal();
    });
  }

  actualizarUser(id: number, state: boolean) {
    this.users.update((lista:any) =>
      lista.map((u:any) => u.id === id ? { ...u, state:state } : u)
    );
  }

  createUser(){
    this.router.navigateByUrl("/user/create");
  }

  editUser(item:any){
    this.router.navigateByUrl("/user/edit/"+item.id);
  }

  verUser(item:any){
    this.router.navigateByUrl("/user/show/"+item.id);
  }

}
