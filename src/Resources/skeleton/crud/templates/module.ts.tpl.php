import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';

import { <?= $entity_class_name ?>RoutingModule } from './<?= $entity_twig_var_singular ?>-routing.module';
import { <?= $entity_class_name ?>NewComponent } from './<?= $entity_twig_var_singular ?>-new/<?= $entity_twig_var_singular ?>-new.component';
import { <?= $entity_class_name ?>EditComponent } from './<?= $entity_twig_var_singular ?>-edit/<?= $entity_twig_var_singular ?>-edit.component';
import { <?= $entity_class_name ?>ItemComponent } from './<?= $entity_twig_var_singular ?>-item/<?= $entity_twig_var_singular ?>-item.component';
import { <?= $entity_class_name ?>ListComponent } from './<?= $entity_twig_var_singular ?>-list/<?= $entity_twig_var_singular ?>-list.component';
import { <?= $entity_class_name ?>ShowComponent } from './<?= $entity_twig_var_singular ?>-show/<?= $entity_twig_var_singular ?>-show.component';
import { FormsModule } from '@angular/forms';
import { DataTablesModule } from 'angular-datatables';
import { FroalaEditorModule, FroalaViewModule } from 'angular-froala-wysiwyg';
import { Ng2SearchPipeModule } from 'ng2-search-filter';
import {NgxPaginationModule} from 'ngx-pagination';
import { TruncateModule } from 'ng2-truncate';

@NgModule({
  declarations: [
    <?= $entity_class_name ?>NewComponent,
    <?= $entity_class_name ?>EditComponent,
    <?= $entity_class_name ?>ItemComponent,
    <?= $entity_class_name ?>ListComponent,
    <?= $entity_class_name ?>ShowComponent
  ],
  imports: [
    CommonModule,
    <?= $entity_class_name ?>RoutingModule,
    FormsModule,
    DataTablesModule,
    FroalaEditorModule.forRoot(),
    FroalaViewModule.forRoot(),
    Ng2SearchPipeModule,
    NgxPaginationModule,
    TruncateModule
  ],
  exports: [
    <?= $entity_class_name ?>NewComponent,
    <?= $entity_class_name ?>EditComponent,
    <?= $entity_class_name ?>ItemComponent,
    <?= $entity_class_name ?>ListComponent,
    <?= $entity_class_name ?>ShowComponent
  ]
})
export class <?= $entity_class_name ?>Module { }
