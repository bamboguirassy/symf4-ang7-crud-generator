import { NgModule, Component } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';
import { <?= $entity_class_name ?>ListComponent } from './<?= $entity_twig_var_singular ?>-list/<?= $entity_twig_var_singular ?>-list.component';
import { <?= $entity_class_name ?>NewComponent } from './<?= $entity_twig_var_singular ?>-new/<?= $entity_twig_var_singular ?>-new.component';
import { <?= $entity_class_name ?>EditComponent } from './<?= $entity_twig_var_singular ?>-edit/<?= $entity_twig_var_singular ?>-edit.component';
import { <?= $entity_class_name ?>ShowComponent } from './<?= $entity_twig_var_singular ?>-show/<?= $entity_twig_var_singular ?>-show.component';
import { <?= $entity_class_name ?>ListSecureService } from './<?= $entity_twig_var_singular ?>-list/<?= $entity_twig_var_singular ?>-list-secure.service';
import { <?= $entity_class_name ?>NewSecureService } from './<?= $entity_twig_var_singular ?>-new/<?= $entity_twig_var_singular ?>-new-secure.service';
import { <?= $entity_class_name ?>EditSecureService } from './<?= $entity_twig_var_singular ?>-edit/<?= $entity_twig_var_singular ?>-edit-secure.service';
import { <?= $entity_class_name ?>ShowSecureService } from './<?= $entity_twig_var_singular ?>-show/<?= $entity_twig_var_singular ?>-show-secure.service';

const routes: Routes = [
  {
    path: '',
    children: [
      {
        path: '',
        component: <?= $entity_class_name ?>ListComponent,
        data: {
          title: 'Liste des <?= $entity_twig_var_plural ?>'
        },
        resolve: {<?= $entity_twig_var_plural ?>:<?= $entity_class_name ?>ListSecureService},
        canActivate: [<?= $entity_class_name ?>ListSecureService],
      },
      {
        path: 'new',
        component: <?= $entity_class_name ?>NewComponent,
        data: {
          title: 'Ajout de <?= $entity_twig_var_singular ?>'
        },
        canActivate: [<?= $entity_class_name ?>NewSecureService],
      },
      {
        path: ':id/edit',
        component: <?= $entity_class_name ?>EditComponent,
        data: {
          title: 'Modification de <?= $entity_twig_var_singular ?>'
        },
        resolve: {<?= $entity_twig_var_singular ?>:<?= $entity_class_name ?>EditSecureService},
        canActivate: [<?= $entity_class_name ?>EditSecureService]
      },
      {
        path: ':id',
        component: <?= $entity_class_name ?>ShowComponent,
        data: {
          title: 'Affichage de <?= $entity_twig_var_singular ?>'
        },
        resolve: {<?= $entity_twig_var_singular ?>:<?= $entity_class_name ?>ShowSecureService},
        canActivate: [<?= $entity_class_name ?>ShowSecureService]
      }
    ]
  }
];

@NgModule({
  imports: [RouterModule.forChild(routes)],
  exports: [RouterModule]
})
export class <?= $entity_class_name ?>RoutingModule { }