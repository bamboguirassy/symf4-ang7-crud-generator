import { Injectable } from '@angular/core';
import { Resolve, ActivatedRouteSnapshot, RouterStateSnapshot, CanLoad } from '@angular/router';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';
import { map, catchError } from 'rxjs/operators';
import { HttpClientService } from 'app/shared/services/http-client.service';
import { of } from 'rxjs';
import { CanActivate } from '@angular/router/src/utils/preactivation';
import { <?= $entity_class_name ?>Model } from '../<?= $entity_twig_var_singular ?>-model';

@Injectable({
  providedIn: 'root'
})
export class <?= $entity_class_name ?>EditSecureService implements Resolve<<?= $entity_class_name ?>Model>,CanActivate {
  path: ActivatedRouteSnapshot[];
  route: ActivatedRouteSnapshot;
  canActivate(route: import("@angular/router").Route, segments: import("@angular/router").UrlSegment[]): boolean | import("rxjs").Observable<boolean> | Promise<boolean> {
    return this.<?= $entity_twig_var_singular ?>Srv.isEditAllowed();
  }
  resolve(route: ActivatedRouteSnapshot,
   state: RouterStateSnapshot) {
    return this.<?= $entity_twig_var_singular ?>Srv.findOneById(route.params.id).pipe(map(data => {
      this.httpClientSrv.handleCustomError(data);
      return this.httpClientSrv.retrieveDataFromResponse(data);
    }),
    catchError(error => {
      this.httpClientSrv.handleSystemError(error);
      const message = `Retrieval error: ${error}`;
      return of({ <?= $entity_twig_var_singular ?>: null, error: message });
    }));
  }

  constructor(private <?= $entity_twig_var_singular ?>Srv:<?= $entity_class_name ?>Service,private httpClientSrv:HttpClientService) { }
}
