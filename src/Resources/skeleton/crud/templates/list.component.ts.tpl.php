import { Component, OnInit, Input, Output } from '@angular/core';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';
import { HttpClientService } from 'app/shared/services/http-client.service';
import { ActivatedRoute, Router } from '@angular/router';
import { <?= $entity_class_name ?>Model } from '../<?= $entity_twig_var_singular ?>-model';

@Component({
  selector: 'app-<?= $entity_twig_var_singular ?>-list',
  templateUrl: './<?= $entity_twig_var_singular ?>-list.component.html',
  styleUrls: ['./<?= $entity_twig_var_singular ?>-list.component.scss']
})
export class <?= $entity_class_name ?>ListComponent implements OnInit {

  @Input() <?= $entity_twig_var_plural ?>: <?= $entity_class_name ?>Model[];
  @Input() listType: string = 'tile';
  @Input() componentTitle: string = 'Liste des <?= $entity_twig_var_plural ?>';
  @Input() isExternalSource: boolean = false;
  dtOptions: DataTables.Settings = {};
  @Input() itemType: string = 'tile';
  @Input() addType: string = 'standalone';
  @Input() itemEditType: string='standalone';


  constructor(private <?= $entity_twig_var_singular ?>Srv: <?= $entity_class_name ?>Service,
    private httpClientSrv: HttpClientService,
    private activatedRoute: ActivatedRoute,
    private router: Router) { }

  ngOnInit() {
    //retrouver les données s'il ne s'agit pas de source externe
    if (!this.isExternalSource) {
      this.<?= $entity_twig_var_plural ?> = this.activatedRoute.snapshot.data['<?= $entity_twig_var_plural ?>'];
    }
    this.dtOptions = {
      pagingType: 'full_numbers',
      pageLength: 10
    };
  }

  refreshList() {
    //s'il ne s'agit pas de source externe, raffraichir en appelant le serveur
    if (!this.isExternalSource) {
      this.<?= $entity_twig_var_singular ?>Srv.findAll().subscribe(data => {
        this.httpClientSrv.handleCustomError(data);
        this.<?= $entity_twig_var_plural ?> = this.httpClientSrv.retrieveDataFromResponse(data);
      }, error => {
        this.httpClientSrv.handleSystemError(error);
      });
    }
  }

  isTile(): boolean {
    return this.listType == 'tile';
  }

  isTable(): boolean {
    return this.listType == 'table';
  }

  remove<?= $entity_class_name ?>(<?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model) {
    this.<?= $entity_twig_var_singular ?>Srv.remove(<?= $entity_twig_var_singular ?>.id)
      .subscribe(data => {
        this.httpClientSrv.handleCustomError(data);
        if (!this.httpClientSrv.isCustomErrorFound(data)) {
          if (!this.isExternalSource) {
            this.refreshList();
          } else {
            this.<?= $entity_twig_var_plural ?>.splice(this.<?= $entity_twig_var_plural ?>.indexOf(<?= $entity_twig_var_singular ?>), 1);
          }
        }
      }, error => {
        this.httpClientSrv.handleCustomError(error);
      });
  }

  goto<?= $entity_class_name ?>New() {
    this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix(), 'new']);
  }

  goto<?= $entity_class_name ?>Show(<?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model) {
    this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix(), <?= $entity_twig_var_singular ?>.id]);
  }

  goto<?= $entity_class_name ?>Edit(<?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model) {
    this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix(), <?= $entity_twig_var_singular ?>.id, 'edit']);
  }

  isAddTypeModal(): boolean {
    return this.addType == 'modal';
  }

  isAddTypeStandalone() {
    return this.addType == 'standalone';
  }

  catchAddEvent(<?= $entity_twig_var_singular ?>:<?= $entity_class_name ?>Model){
    if(this.isExternalSource){
      this.<?= $entity_twig_var_plural ?>.push(<?= $entity_twig_var_singular ?>);
    } else {
      this.refreshList();
    }
  }

  isItemEditTypeStandalone(): boolean {
    return this.itemEditType == 'standalone';
  }

  isItemEditTypeModal(): boolean {
    return this.itemEditType == 'modal';
  }

}
