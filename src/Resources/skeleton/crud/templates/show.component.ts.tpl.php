import { Component, OnInit, Input } from '@angular/core';
import { <?= $entity_class_name ?>Service } from '../<?= $entity_twig_var_singular ?>.service';
import { ActivatedRoute, Router } from '@angular/router';
import { Location } from '@angular/common';
import { <?= $entity_class_name ?>Model } from '../<?= $entity_twig_var_singular ?>-model';

@Component({
  selector: 'app-<?= $entity_twig_var_singular ?>-show',
  templateUrl: './<?= $entity_twig_var_singular ?>-show.component.html',
  styleUrls: ['./<?= $entity_twig_var_singular ?>-show.component.scss']
})
export class <?= $entity_class_name ?>ShowComponent implements OnInit {

  @Input() <?= $entity_twig_var_singular ?>: <?= $entity_class_name ?>Model;
  @Input() type: string = 'tile';
  @Input() componentTitle: string = 'Détails catégorie';
  @Input() isExternalSource: boolean = false;
  @Input() showRelatedData: boolean = true;
  @Input() itemEditType: string = 'standalone';


  constructor(private <?= $entity_twig_var_singular ?>Srv: <?= $entity_class_name ?>Service,
    private activatedRoute: ActivatedRoute,
    private router: Router, private location: Location) {}

  ngOnInit() {
    if (!this.isExternalSource) {
      this.<?= $entity_twig_var_singular ?> = this.activatedRoute.snapshot.data['<?= $entity_twig_var_singular ?>'];
    }
  }

  onDelete() {
    this.router.navigate([this.<?= $entity_twig_var_singular ?>Srv.getRoutePrefix()]);
  }

  goBack() {
    this.location.back();
  }

}
