<section id="simple-table" [hidden]="!<?= $entity_twig_var_singular ?>Srv.isListingAllowed()">
  <!-- table section -->
  <div class="row text-left">
    <div class="col-sm-12">
      <div class="card">
        <div class="card-header">
          <h4 class="card-title" id="diapal-title">{{componentTitle}}</h4>
          <!-- <hr style="height: 50px;"> -->
          <hr id="diapal-line-separtor">
          <div class="row">
            <div class="col-md-8 col-sm-12 col-lg-8">
              <p>
                Desc.
              </p>
            </div>
            <div class="col-md-4 col-sm-12 col-lg-4">
              <div class="row">
                <div class="col-md-8 col-sm-8 col-lg-8" [hidden]="isTable()">
                  <input type="text" class="form-control" [(ngModel)]="<?= $entity_twig_var_singular ?>Filter"
                    placeholder="Taper pour rechercher" />
                </div>
                <div class="col-md-4 col-sm-4 col-lg-4">
                  <a class="btn btn-md btn-facebook" id="diapal-button"
                    [hidden]="!<?= $entity_twig_var_singular ?>Srv.isAddAllowed() || !isAddTypeStandalone()" (click)="goto<?= $entity_class_name ?>New()"><i
                      class="ft ft-plus-square"></i></a>
                  <app-<?= $entity_twig_var_singular ?>-new *ngIf="isAddTypeModal()" (addEvent)="catchAddEvent($event)" [type]='addType'>
                  </app-<?= $entity_twig_var_singular ?>-new>
                  <button type="button" (click)="refreshList()"
                    [hidden]="!<?= $entity_twig_var_singular ?>Srv.isListingAllowed() || isExternalSource"
                    class="btn btn-facebook ml-1 gradient-blackberry">
                    <i class="fa fa-refresh" aria-hidden="true"></i>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="card-content">
          <div class="card-body">
            <div class="row" [hidden]="isTile()">
              <div class="col-sm-12 col-md-12 col-lg-12">
                <table datatable [dtOptions]="dtOptions" class="table table-striped table-bordered table-hover">
                  <thead id="diapal-table-header">
                    <tr>
                        <?php $i=0; ?>
                      <?php foreach ($entity_fields as $field): ?>
                        <?php if($i!=0): ?>
                        <th><?= ucfirst($field['fieldName']) ?></th>
                        <?php endif; ?>
                        <?php $i++; ?>
                      <?php endforeach; ?>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr *ngFor="let <?= $entity_twig_var_singular ?> of <?= $entity_twig_var_plural ?>">
                        <?php $i=0; ?>
                        <?php foreach ($entity_fields as $field): ?>
                        <?php if($i!=0): ?>
                      <td>{{ <?= $entity_twig_var_singular ?>.<?= $field['fieldName'] ?> }}</td>
                        <?php endif; ?>
                        <?php $i++; ?>
                          <?php endforeach; ?>
                      <td>
                        <a (click)="goto<?= $entity_class_name ?>Show(<?= $entity_twig_var_singular ?>)" class="btn mr-1 btn-facebook btn-sm"
                          id="diapal-button"><i class="ft ft-eye"></i></a>
                        <a *ngIf="<?= $entity_twig_var_singular ?>Srv.isEditAllowed() && isItemEditTypeStandalone()"
                          (click)="goto<?= $entity_class_name ?>Edit(<?= $entity_twig_var_singular ?>)" class="btn mr-1 btn-soundcloud btn-sm"><i
                            class="ft ft-edit"></i></a>
                        <app-<?= $entity_twig_var_singular ?>-edit [isExternalSource]="true" *ngIf="isItemEditTypeModal()"
                          [type]="itemEditType" [<?= $entity_twig_var_singular ?>]="<?= $entity_twig_var_singular ?>"></app-<?= $entity_twig_var_singular ?>-edit>
                        <button (click)="remove<?= $entity_class_name ?>(<?= $entity_twig_var_singular ?>)" class="btn mr-1 btn-danger btn-fab btn-sm">
                          <i class="ft ft-trash-2"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <!-- tile section-->
            <div class="row" [hidden]="isTable()">
              <div class="col-sm-12 col-md-6 col-lg-4"
                *ngFor="let <?= $entity_twig_var_singular ?> of <?= $entity_twig_var_plural ?>|filter:<?= $entity_twig_var_singular ?>Filter| paginate: { itemsPerPage: 3, currentPage: p }">
                <app-<?= $entity_twig_var_singular ?>-item [type]="itemType" [<?= $entity_twig_var_singular ?>]="<?= $entity_twig_var_singular ?>" [isShowButtonVisible]="true"
                  [isEditButtonVisible]="true" [itemEditType]="itemEditType" [isDeleteButtonVisible]="true" (suppressionEvent)="refreshList()">
                </app-<?= $entity_twig_var_singular ?>-item>
              </div>
              <div class="col-sm-12 col-md-12 col-lg-12 pull-right">
                <pagination-controls class="pull-right" (pageChange)="p = $event"></pagination-controls>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>