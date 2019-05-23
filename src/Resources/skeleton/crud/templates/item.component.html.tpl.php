<!-- tile -->
<div class="col-md-12" [hidden]="!<?= $entity_twig_var_singular ?>Srv.isReadAllowed()">
  <div class="card" [hidden]="!isTile()">
    <div class="card-header text-white" id="diapal-card-header">
      <h4 class="card-title">{{ <?= $entity_twig_var_singular ?>?.nom }} <i class="ft ft-box pull-right"></i></h4>
    </div>
    <div class="card-body  height-200 border">
      <p class="card-text" [froalaView]='<?= $entity_twig_var_singular ?>?.description|truncate:1000:"..."'></p>
    </div>
    <div class="card-footer" id="diapal-card-footer">
      <ng-template [ngTemplateOutlet]="buttonTemplate">
      </ng-template>
    </div>
  </div>
  <!-- table -->
  <table class="table table-bordered table-striped" [hidden]="!isTable()">
    <thead id="diapal-table-header">
      <tr>
        <th colspan="2">
          Information de base
        </th>
      </tr>
    </thead>
    <tbody>
        <?php $i=0; ?>
        <?php foreach ($entity_fields as $field): ?>
        <?php if($i!=0): ?>
      <tr>
        <th><?= ucfirst($field['fieldName']) ?></th>
        <td>{{<?= $entity_twig_var_singular ?>?.<?= $field['fieldName'] ?>}}</td>
      </tr>
      <?php endif; ?>
      <?php $i++; ?>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="2">
          <ng-template [ngTemplateOutlet]="buttonTemplate">
          </ng-template>
        </th>
      </tr>
    </tfoot>
  </table>
  <!-- list -->
  <div class="card" [hidden]="!isList()">
    <div class="card-header text-white" id="diapal-card-header">
      <h4 class="card-title">{{ <?= $entity_twig_var_singular ?>?.nom }}<i class="ft ft-box pull-right"></i></h4>
    </div>
    <div class="card-body border height-200">
      <div class="row">
        <div class="col-md-12">
          <ul class="no-list-style">
              <?php $i=0; ?>
              <?php foreach ($entity_fields as $field): ?>
              <?php if($i!=0): ?>
            <li class="mb-2">
              <span class="text-bold-500 primary"><a><i class="icon-present font-small-3"></i> <?= ucfirst($field['fieldName']) ?>:</a></span>
              <span class="d-block overflow-hidden">{{<?= $entity_twig_var_singular ?>?.<?= $field['fieldName'] ?>}}</span>
            </li>
            <?php endif; ?>
            <?php $i++; ?>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
    <div class="card-footer text-muted" id="diapal-card-footer">
      <ng-template [ngTemplateOutlet]="buttonTemplate">
      </ng-template>
    </div>
  </div>
</div>

<!-- template des buttons -->
<ng-template #buttonTemplate>
  <a *ngIf="isShowButtonVisible && <?= $entity_twig_var_singular ?>Srv.isReadAllowed()" class="btn btn-facebook pull-right mr-1"
    (click)="goto<?= $entity_class_name ?>Show()"><i class="ft ft-eye"></i></a>
  <a *ngIf="isEditButtonVisible && <?= $entity_twig_var_singular ?>Srv.isEditAllowed() && isItemEditTypeStandalone()"
    class="btn btn-raised btn-warning pull-right mr-1" (click)="goto<?= $entity_class_name ?>Edit()" title="Modifier"><i
      class="ft ft-edit-3"></i></a>
  <app-<?= $entity_twig_var_singular ?>-edit [isExternalSource]="true" *ngIf="isItemEditTypeModal()"  [type]="itemEditType" [<?= $entity_twig_var_singular ?>]="<?= $entity_twig_var_singular ?>"></app-<?= $entity_twig_var_singular ?>-edit>
  <button *ngIf="isDeleteButtonVisible && <?= $entity_twig_var_singular ?>Srv.isDeleteAllowed()"
    class="btn btn-raised btn-danger pull-right mr-1" (click)="remove<?= $entity_class_name ?>()" title="Supprimer"><i
      class="ft ft-trash-2"></i></button>
</ng-template>