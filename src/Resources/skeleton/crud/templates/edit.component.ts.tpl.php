import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';
import { ActivatedRoute, Router } from '@angular/router';
import { HttpClientService } from 'app/shared/services/http-client.service';
import { Location } from '@angular/common';
import { <?= $entity_class_name ?>Model } from '../<?= $entity_twig_var_singular ?>-model';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-<?= $entity_twig_var_singular ?>-edit',
  templateUrl: './<?= $entity_twig_var_singular ?>-edit.component.html',
  styleUrls: ['./<?= $entity_twig_var_singular ?>-edit.component.scss']
})
export class <?= $entity_class_name ?>EditComponent implements OnInit {

  @Input() <?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model;
  @Input() componentTitle: string = 'Formulaire de Modification - <?= $entity_class_name ?>';
  @Input() isExternalSource: boolean = false;
  @Input() type: string = 'standalone';
  @Output() editEvent = new EventEmitter<<?= $entity_class_name ?>Model>();

  constructor(private <?= $entity_twig_var_singular ?>Srv: <?= $entity_class_name ?>Service,
    private activatedRoute: ActivatedRoute,
    private httpClientSrv: HttpClientService,
    private router: Router, private location: Location,
    private modalService: NgbModal) { }

  ngOnInit() {
    if (!this.isExternalSource) {
      this.<?= $entity_twig_var_singular ?> = this.activatedRoute.snapshot.data['<?= $entity_twig_var_singular ?>'];
    }
  }

  update<?= $entity_class_name ?>() {
    this.<?= $entity_twig_var_singular ?>Srv.update(this.<?= $entity_twig_var_singular ?>)
      .subscribe(data => {
        this.httpClientSrv.handleCustomError(data);
        this.<?= $entity_twig_var_singular ?> = this.httpClientSrv.retrieveDataFromResponse(data);
        if (!this.isExternalSource) {
          this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix(), this.<?= $entity_twig_var_singular ?>.id]);
        }
      }, error => {
        this.httpClientSrv.handleSystemError(error);
      });
  }

  goBack() {
    this.location.back();
  }

  isStandalone(): boolean {
    return this.type == 'standalone';
  }

  isModal(): boolean {
    return this.type == 'modal';
  }

  open<?= $entity_class_name ?>EditModal(content) {
    this.modalService.open(content, { centered: true, size: 'lg' }).result.then((result) => {
      //update the <?= $entity_twig_var_singular ?>
      
      this.<?= $entity_twig_var_singular ?>Srv.update(this.<?= $entity_twig_var_singular ?>)
      .subscribe(data => {
        this.httpClientSrv.handleCustomError(data);
        this.<?= $entity_twig_var_singular ?> = this.httpClientSrv.retrieveDataFromResponse(data);
        this.editEvent.emit(this.<?= $entity_twig_var_singular ?>);
      }, error => {
        this.httpClientSrv.handleSystemError(error);
      });
    }, (reason) => {
    });
  }

}
