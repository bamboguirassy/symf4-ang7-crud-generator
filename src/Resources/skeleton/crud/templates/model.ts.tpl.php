export class <?= $entity_class_name ?>Model {
<?php foreach ($entity_fields as $field): ?>

    <?= $field['fieldName'] ?>: any;

<?php endforeach; ?>
}