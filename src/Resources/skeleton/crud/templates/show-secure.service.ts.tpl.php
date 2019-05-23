import { Injectable } from '@angular/core';
import { Resolve, CanActivate } from '@angular/router';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';
import { map, catchError } from 'rxjs/operators';
import { HttpClientService } from 'app/shared/services/http-client.service';
import { of } from 'rxjs';
import { <?= $entity_class_name ?>Model } from '../<?= $entity_twig_var_singular ?>-model';

@Injectable({
  providedIn: 'root'
})
export class <?= $entity_class_name ?>ShowSecureService implements Resolve<<?= $entity_class_name ?>Model>,CanActivate {
  canActivate(route: import("@angular/router").ActivatedRouteSnapshot, state: import("@angular/router").RouterStateSnapshot): boolean | import("@angular/router").UrlTree | import("rxjs").Observable<boolean | import("@angular/router").UrlTree> | Promise<boolean | import("@angular/router").UrlTree> {
    return this.<?= $entity_twig_var_singular ?>Srv.isReadAllowed();
  }
  resolve(route: import("@angular/router").ActivatedRouteSnapshot, state: import("@angular/router").RouterStateSnapshot) {
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

  constructor(private <?= $entity_twig_var_singular ?>Srv: <?= $entity_class_name ?>Service, private httpClientSrv: HttpClientService) { }
}
