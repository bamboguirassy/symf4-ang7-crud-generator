<section id="basic-form-layouts" [hidden]="!<?= $entity_twig_var_singular ?>Srv.isEditAllowed() || !isStandalone()">
  <div class="row text-left">
    <div class="col-md-12">
      <div class="card">
        <div class="card-header pb-2">
          <h4 class="card-title" id="diapal-title">{{componentTitle}}</h4>
          <hr id="diapal-line-separtor">
          <p class="mb-0">Desc.</p>
        </div>
        <div class="card-content">
          <div class="px-3">
            <ng-template [ngTemplateOutlet]="<?= $entity_twig_var_singular ?>EditForm"></ng-template>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- modal section -->
<ng-template #content let-modal>
  <div class="modal-header" id="diapal-modal-header">
    <h4 class="modal-title text-white" id="modal-basic-title">{{componentTitle}}</h4>
    <button type="button" class="close" aria-label="Close" (click)="modal.dismiss()">
      <span aria-hidden="true" style="color: white;">&times;</span>
    </button>
  </div>
  <div class="modal-body">
    <ng-template [ngTemplateOutlet]="<?= $entity_twig_var_singular ?>EditForm"></ng-template>
  </div>
  <div class="modal-footer" id="diapal-modal-footer">
    <button type="button" class="btn btn-raised" id="diapal-white-button" (click)="modal.close()">Mettre à jour</button>
  </div>
</ng-template>
<button [hidden]="!isModal()" class="btn mr-1 btn-soundcloud btn-raised pull-right" (click)="open<?= $entity_class_name ?>EditModal(content)"><i
  class="ft ft-edit"></i></button>

<ng-template #<?= $entity_twig_var_singular ?>EditForm>
  <form class="form" #<?= $entity_twig_var_singular ?>Form="ngForm" (ngSubmit)="update<?= $entity_class_name ?>(<?= $entity_twig_var_singular ?>Form)">
    <div class="form-body">
      <h4 class="form-section" id="diapal-title"><i class="ft-user"></i> Informations de base</h4>
      <div class="row">
          <?php $i=0; ?>
          <?php foreach ($entity_fields as $field): ?>
          <?php if($i!=0): ?>
        <div class="col-md-6">
          <div class="form-group">
            <label for="<?= $field['fieldName'] ?>"><?= ucfirst($field['fieldName']) ?></label>
            <input type="text" id="<?= $field['fieldName'] ?>" class="form-control" name="<?= $field['fieldName'] ?>" [(ngModel)]="<?= $entity_twig_var_singular ?>.<?= $field['fieldName'] ?>" required>
          </div>
        </div>
          <?php endif; ?>
          <?php $i++; ?>
          <?php endforeach; ?>
      </div>
      <h4 class="form-section" id="diapal-title"><i class="ft-file-text"></i> Infos Supplémentaires</h4>
      <div class="form-group">
        <label for="description">Description</label>
        <textarea [froalaEditor] id="description" rows="5" class="form-control" name="description"
          [(ngModel)]="<?= $entity_twig_var_singular ?>.description" required></textarea>
      </div>
    </div>

    <div class="form-actions" [hidden]="!isStandalone()">
      <a class="btn btn-raised btn-bitbucket mr-1" (click)="goBack()" id="diapal-button">
        <i class="fa fa-reply"></i> Retour
      </a>
      <button *ngIf="<?= $entity_twig_var_singular ?>Srv.isEditAllowed()" type="submit" class="btn btn-raised btn-facebook pull-right"
        id="diapal-button" (click)="update<?= $entity_class_name ?>()">
        <i class="fa fa-check-square-o"></i> Mettre à jour
      </button>
    </div>
  </form>
</ng-template>