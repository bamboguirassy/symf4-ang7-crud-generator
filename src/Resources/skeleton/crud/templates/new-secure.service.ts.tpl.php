import { Injectable } from '@angular/core';
import { CanActivate, Resolve } from '@angular/router';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';

@Injectable({
  providedIn: 'root'
})
export class <?= $entity_class_name ?>NewSecureService implements CanActivate,Resolve<any> {
  canActivate(route: import("@angular/router").ActivatedRouteSnapshot, state: import("@angular/router").RouterStateSnapshot): boolean | import("@angular/router").UrlTree | import("rxjs").Observable<boolean | import("@angular/router").UrlTree> | Promise<boolean | import("@angular/router").UrlTree> {
    return this.<?= $entity_twig_var_singular ?>Srv.isAddAllowed();
  }
  resolve(route: import("@angular/router").ActivatedRouteSnapshot, state: import("@angular/router").RouterStateSnapshot) {
    throw new Error("Method not implemented.");
  }

  constructor(private <?= $entity_twig_var_singular ?>Srv:<?= $entity_class_name ?>Service) { }
}
