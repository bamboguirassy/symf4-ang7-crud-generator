import { Injectable } from '@angular/core';
import { HttpClientService } from 'app/shared/services/http-client.service';
import { ToasterService } from 'app/shared/services/toaster.service';
import { Subject, Observable, BehaviorSubject } from 'rxjs';
import { UserService } from 'app/user/user.service';
import { <?= $entity_class_name ?>Model } from './<?= $entity_twig_var_singular ?>-model';

@Injectable({
  providedIn: 'root'
})
export class <?= $entity_class_name ?>Service {

  constructor(private httpSrv: HttpClientService, private toastr: ToasterService, private userSrv: UserService) { }

  findAll() {
    return this.httpSrv.get(this.getRoutePrefix()+'/');
  }

  findOneById(id: string) {
    return this.httpSrv.get(this.getRoutePrefix()+'/' + id);
  }

  update(<?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model) {
    return this.httpSrv.put(this.getRoutePrefix()+'/' + <?= $entity_twig_var_singular ?>.id + '/edit', <?= $entity_twig_var_singular ?>);
  }

  save(<?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model) {
    return this.httpSrv.post(this.getRoutePrefix()+'/new', <?= $entity_twig_var_singular ?>);
  }

  remove(id: any) {
    return this.httpSrv.remove(this.getRoutePrefix()+'/' + id);
  }

  getRoutePrefix():string {
    return '<?= $entity_twig_var_singular ?>';
  }

  isReadAllowed():boolean {
    return this.userSrv.checkReadableAccess(this.getRoutePrefix());
  }

  isAddAllowed():boolean {
    return this.userSrv.checkCreableAccess(this.getRoutePrefix());
  }

  isDeleteAllowed():boolean {
    return this.userSrv.checkDeletableAccess(this.getRoutePrefix());
  }

  isListingAllowed():boolean {
    return this.userSrv.checkListableAccess(this.getRoutePrefix());
  }

  isEditAllowed():boolean {
    return this.userSrv.checkEditbleAccess(this.getRoutePrefix());
  }
}