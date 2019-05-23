<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $entity_full_class_name ?>;
use <?= $form_full_class_name ?>;
<?php if (isset($repository_full_class_name)): ?>
use <?= $repository_full_class_name ?>;
<?php endif ?>
use Symfony\Bundle\FrameworkBundle\Controller\<?= $parent_class_name ?>;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\Utils\PResponse;
use App\Utils\PUtils;

/**
 * @Route("/api<?= $route_path ?>")
 */
class <?= $class_name ?> extends <?= $parent_class_name; ?><?= "\n" ?>
{
    /**
     * @Rest\Get(path="/", name="<?= $route_name ?>_index")
     * @Rest\View(StatusCode = 200)
     */
<?php if (isset($repository_full_class_name)): ?>
    public function index(<?= $repository_class_name ?> $<?= $repository_var ?>): PResponse
    {
        $response = new PResponse();
        $<?= $entity_twig_var_plural ?> = $<?= $repository_var ?>->findAll();
        $response->setData($<?= $entity_twig_var_plural ?>);
        return $response;
    }
<?php else: ?>
    public function index(): PResponse
    {
        $response = new PResponse();
        $<?= $entity_var_plural ?> = $this->getDoctrine()
            ->getRepository(<?= $entity_class_name ?>::class)
            ->findAll();

        $response->setData($<?= $entity_twig_var_plural ?>);
        return $response;
    }
<?php endif ?>

    /**
     * @Rest\Post(Path="/new", name="<?= $route_name ?>_new")
     * @Rest\View(StatusCode=200)
     */
    public function add(Request $request): PResponse
    {
        $$<?= $entity_var_singular ?>=new <?= $entity_class_name ?>();
        $response = new PResponse();
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(<?= $entity_class_name ?>Type::class, $<?= $entity_var_singular ?>);
        $form->submit(PUtils::serializeRequestContent($request));
        $entityManager->persist($<?= $entity_var_singular ?>);
        $entityManager->flush();
        $response->setData($<?= $entity_var_singular ?>);
        return $response;
    }

    /**
     * @Rest\Get(path="/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_show",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     */
    public function show(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): PResponse
    {
        $response = new PResponse();
        $response->setData($<?= $entity_var_singular ?>);
        return $response;
    }

    /**
     * @Rest\Put(path="/{<?= $entity_identifier ?>}/edit", name="<?= $route_name ?>_edit",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     */
    public function edit(Request $request, <?= $entity_class_name ?> $<?= $entity_var_singular ?>): PResponse
    {   $response = new PResponse();
        $form = $this->createForm(<?= $form_class_name ?>::class, $<?= $entity_var_singular ?>);
        $form->submit(PUtils::serializeRequestContent($request));
        $this->getDoctrine()->getManager()->flush();
        $response->setData($<?= $entity_var_singular ?>);
        return $response;
    }

    /**
     * @Rest\Delete("/{<?= $entity_identifier ?>}", name="<?= $route_name ?>_delete",requirements = {"id"="\d+"})
     * @Rest\View(StatusCode=200)
     */
    public function delete(<?= $entity_class_name ?> $<?= $entity_var_singular ?>): PResponse
    {
        $response = new PResponse();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($<?= $entity_var_singular ?>);
        $entityManager->flush();
        $response->setData($<?= $entity_var_singular ?>);
        return $response;
    }
}
