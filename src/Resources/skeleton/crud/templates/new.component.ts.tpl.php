import { Component, OnInit, Input, Output, EventEmitter } from '@angular/core';
import { NgForm } from '@angular/forms';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';
import { HttpClientService } from 'app/shared/services/http-client.service';
import { Router } from '@angular/router';
import { Location } from '@angular/common';
import { <?= $entity_class_name ?>Model } from '../<?= $entity_twig_var_singular ?>-model';
import { NgbModal } from '@ng-bootstrap/ng-bootstrap';

@Component({
  selector: 'app-<?= $entity_twig_var_singular ?>-new',
  templateUrl: './<?= $entity_twig_var_singular ?>-new.component.html',
  styleUrls: ['./<?= $entity_twig_var_singular ?>-new.component.scss']
})
export class <?= $entity_class_name ?>NewComponent implements OnInit {

  @Input() componentTitle: string = 'Formulaire de Création - <?= $entity_class_name ?>'
  @Input() <?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model;
  @Input() type: string = 'standalone';
  @Output() addEvent=new EventEmitter<<?= $entity_class_name ?>Model>();

  constructor(private <?= $entity_twig_var_singular ?>Srv: <?= $entity_class_name ?>Service,
    private httpClientSrv: HttpClientService,
    private router: Router, private location: Location,
    private modalService: NgbModal) {
    if (this.<?= $entity_twig_var_singular ?> == null) {
      this.<?= $entity_twig_var_singular ?> = new <?= $entity_class_name ?>Model();
    }
  }

  ngOnInit() {
  }

  create<?= $entity_class_name ?>() {
    this.<?= $entity_twig_var_singular ?>Srv.save(this.<?= $entity_twig_var_singular ?>)
      .subscribe((data) => {
        this.httpClientSrv.handleCustomError(data);
        if (!this.httpClientSrv.isCustomErrorFound(data)) {
          let <?= $entity_twig_var_singular ?> = this.httpClientSrv.retrieveDataFromResponse(data);
          this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix(), <?= $entity_twig_var_singular ?>.id]);
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

  open<?= $entity_class_name ?>NewModal(content) {
    this.modalService.open(content, { centered: true, size: 'lg' }).result.then((result) => {
      //save the <?= $entity_twig_var_singular ?>
      
      this.<?= $entity_twig_var_singular ?>Srv.save(this.<?= $entity_twig_var_singular ?>)
        .subscribe((data) => {
          this.httpClientSrv.handleCustomError(data);
          if (!this.httpClientSrv.isCustomErrorFound(data)) {
            let <?= $entity_twig_var_singular ?> = this.httpClientSrv.retrieveDataFromResponse(data);
            this.addEvent.emit(<?= $entity_twig_var_singular ?>);
            this.<?= $entity_twig_var_singular ?>=new <?= $entity_class_name ?>Model();
            //lancer un evenement d'ajout
          }
        }, error => {
          this.httpClientSrv.handleSystemError(error);
        });
    }, (reason) => {
    });
  }


}
