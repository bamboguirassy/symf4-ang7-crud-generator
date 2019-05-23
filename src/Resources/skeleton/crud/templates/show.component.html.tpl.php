<!-- Content types section start -->
<section id="content-types" [hidden]="!<?= $entity_twig_var_singular ?>Srv.isReadAllowed()">
  <div class="card">
    <div class="card-header">
      <h4 class="card-title" id="diapal-text-color">{{componenTitle}}</h4>
      <hr id="diapal-line-separtor">
      <div class="row">
        <div class="col-md-1">
          <a title="retour à la liste" id="diapal-button" class="btn btn-raised btn-bitbucket mr-1" (click)="goBack()">
            <i class="fa fa-reply"></i>
          </a>
        </div>
        <div class="col-md-11">
          <p>
            Desc.
          </p>
        </div>
      </div>
    </div>
    <div class="card-content">
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <app-<?= $entity_twig_var_singular ?>-item [type]="type" [isEditButtonVisible]="true" [isDeleteButtonVisible]="true"
              [<?= $entity_twig_var_singular ?>]="<?= $entity_twig_var_singular ?>" (suppressionEvent)="onDelete($event)" [itemEditType]="itemEditType"></app-<?= $entity_twig_var_singular ?>-item>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>