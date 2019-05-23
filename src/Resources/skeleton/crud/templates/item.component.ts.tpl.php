import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';
import { HttpClientService } from 'app/shared/services/http-client.service';
import { map } from 'rxjs/operators';
import { Router } from '@angular/router';
import { <?= $entity_class_name ?>Model } from '../<?= $entity_twig_var_singular ?>-model';

@Component({
  selector: 'app-<?= $entity_twig_var_singular ?>-item',
  templateUrl: './<?= $entity_twig_var_singular ?>-item.component.html',
  styleUrls: ['./<?= $entity_twig_var_singular ?>-item.component.scss']
})
export class <?= $entity_class_name ?>ItemComponent implements OnInit {

  @Input() <?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model;
  @Input() isShowButtonVisible: boolean;
  @Input() isEditButtonVisible: boolean;
  @Input() isDeleteButtonVisible: boolean;
  @Input() type: string;
  @Input() itemEditType: string = 'standalone';

  @Output() suppressionEvent = new EventEmitter<any>();

  constructor(private <?= $entity_twig_var_singular ?>Srv: <?= $entity_class_name ?>Service,
    private httpClientSrv: HttpClientService, private router: Router) { }

  ngOnInit() {
  }

  remove<?= $entity_class_name ?>() {
    this.<?= $entity_twig_var_singular ?>Srv.remove(this.<?= $entity_twig_var_singular ?>.id)
      .subscribe(data => {
        this.httpClientSrv.handleCustomError(data);
        if (!this.httpClientSrv.isCustomErrorFound(data)) {
          this.suppressionEvent.emit(true);
        }
      }, error => {
        this.httpClientSrv.handleSystemError(error);
      });

  }

  isTile(): boolean {
    return this.type == 'tile';
  }

  isTable(): boolean {
    return this.type == 'table';
  }

  isList(): boolean {
    return this.type == 'list';
  }

  goto<?= $entity_class_name ?>Show() {
    this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix(), this.<?= $entity_twig_var_singular ?>.id]);
  }

  goto<?= $entity_class_name ?>Edit() {
    this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix(), this.<?= $entity_twig_var_singular ?>.id, 'edit']);
  }

  isItemEditTypeModal() {
    return this.itemEditType == 'modal';
  }

  isItemEditTypeStandalone() {
    return this.itemEditType == 'standalone'
  }

}
