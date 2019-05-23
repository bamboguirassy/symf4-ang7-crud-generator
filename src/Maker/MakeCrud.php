<?php

/*
 * This file is part of the Symfony MakerBundle package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\MakerBundle\Maker;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Common\Inflector\Inflector;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Doctrine\DoctrineHelper;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Renderer\FormTypeRenderer;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Validator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Symfony\Component\Validator\Validation;

/**
 * @author Sadicov Vladimir <sadikoff@gmail.com>
 */
final class MakeCrud extends AbstractMaker {

    private $doctrineHelper;
    private $formTypeRenderer;

    public function __construct(DoctrineHelper $doctrineHelper, FormTypeRenderer $formTypeRenderer) {
        $this->doctrineHelper = $doctrineHelper;
        $this->formTypeRenderer = $formTypeRenderer;
    }

    public static function getCommandName(): string {
        return 'make:crud';
    }

    /**
     * {@inheritdoc}
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig) {
        $command
                ->setDescription('Creates CRUD for Doctrine entity class')
                ->addArgument('entity-class', InputArgument::OPTIONAL, sprintf('The class name of the entity to create CRUD (e.g. <fg=yellow>%s</>)', Str::asClassName(Str::getRandomTerm())))
                ->setHelp(file_get_contents(__DIR__ . '/../Resources/help/MakeCrud.txt'))
        ;

        $inputConfig->setArgumentAsNonInteractive('entity-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command) {
        if (null === $input->getArgument('entity-class')) {
            $argument = $command->getDefinition()->getArgument('entity-class');

            $entities = $this->doctrineHelper->getEntitiesForAutocomplete();

            $question = new Question($argument->getDescription());
            $question->setAutocompleterValues($entities);

            $value = $io->askQuestion($question);

            $input->setArgument('entity-class', $value);
        }
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator) {
        $entityClassDetails = $generator->createClassNameDetails(
                Validator::entityExists($input->getArgument('entity-class'), $this->doctrineHelper->getEntitiesForAutocomplete()), 'Entity\\'
        );

        $entityDoctrineDetails = $this->doctrineHelper->createDoctrineDetails($entityClassDetails->getFullName());

        $repositoryVars = [];

        if (null !== $entityDoctrineDetails->getRepositoryClass()) {
            $repositoryClassDetails = $generator->createClassNameDetails(
                    '\\' . $entityDoctrineDetails->getRepositoryClass(), 'Repository\\', 'Repository'
            );

            $repositoryVars = [
                'repository_full_class_name' => $repositoryClassDetails->getFullName(),
                'repository_class_name' => $repositoryClassDetails->getShortName(),
                'repository_var' => lcfirst(Inflector::singularize($repositoryClassDetails->getShortName())),
            ];
        }

        $controllerClassDetails = $generator->createClassNameDetails(
                $entityClassDetails->getRelativeNameWithoutSuffix() . 'Controller', 'Controller\\', 'Controller'
        );

        $iter = 0;
        do {
            $formClassDetails = $generator->createClassNameDetails(
                    $entityClassDetails->getRelativeNameWithoutSuffix() . ($iter ?: '') . 'Type', 'Form\\', 'Type'
            );
            ++$iter;
        } while (class_exists($formClassDetails->getFullName()));

        $entityVarPlural = lcfirst(Inflector::pluralize($entityClassDetails->getShortName()));
        $entityVarSingular = lcfirst(Inflector::singularize($entityClassDetails->getShortName()));

        $entityTwigVarPlural = Str::asTwigVariable($entityVarPlural);
        $entityTwigVarSingular = Str::asTwigVariable($entityVarSingular);

        $routeName = Str::asRouteName($controllerClassDetails->getRelativeNameWithoutSuffix());
        $templatesPath = Str::asFilePath($controllerClassDetails->getRelativeNameWithoutSuffix());

        $generator->generateController(
                $controllerClassDetails->getFullName(), 'crud/controller/Controller.tpl.php', array_merge([
            'entity_full_class_name' => $entityClassDetails->getFullName(),
            'entity_class_name' => $entityClassDetails->getShortName(),
            'form_full_class_name' => $formClassDetails->getFullName(),
            'form_class_name' => $formClassDetails->getShortName(),
            'route_path' => Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix()),
            'route_name' => $routeName,
            'templates_path' => $templatesPath,
            'entity_var_plural' => $entityVarPlural,
            'entity_twig_var_plural' => $entityTwigVarPlural,
            'entity_var_singular' => $entityVarSingular,
            'entity_twig_var_singular' => $entityTwigVarSingular,
            'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                        ], $repositoryVars
                )
        );

        $this->formTypeRenderer->render(
                $formClassDetails, $entityDoctrineDetails->getFormFields(), $entityClassDetails
        );

        $templates = [
//            '_delete_form' => [
//                'route_name' => $routeName,
//                'entity_twig_var_singular' => $entityTwigVarSingular,
//                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
//            ],
//            '_form' => [],
            $templatesPath.'.service.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '.service.ts',
                'template_path'=>'crud/templates/service.ts.tpl.php',
            ],
            $templatesPath.'.module.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '.module.ts',
                'template_path'=>'crud/templates/module.ts.tpl.php',
            ],
            $templatesPath.'-routing.module.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-routing.module.ts',
                'template_path'=>'crud/templates/routing.module.ts.tpl.php',
            ],
            $templatesPath.'-model.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-model.ts',
                'template_path'=>'crud/templates/model.ts.tpl.php',
            ],
            //edit folder
            $templatesPath.'-edit-secure.service.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-edit/'.$templatesPath.'-edit-secure.service.ts',
                'template_path'=>'crud/templates/edit-secure.service.ts.tpl.php',
            ],
            $templatesPath.'-edit.component.html'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-edit/'.$templatesPath.'-edit.component.html',
                'template_path'=>'crud/templates/edit.component.html.tpl.php',
            ],
            $templatesPath.'-edit.component.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-edit/'.$templatesPath.'-edit.component.ts',
                'template_path'=>'crud/templates/edit.component.ts.tpl.php',
            ],
            $templatesPath.'-edit.component.scss'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-edit/'.$templatesPath.'-edit.component.scss',
                'template_path'=>'crud/templates/edit.component.scss.tpl.php',
            ],
            //item folder
            $templatesPath.'-item.component.html'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-item/'.$templatesPath.'-item.component.html',
                'template_path'=>'crud/templates/item.component.html.tpl.php',
            ],
            $templatesPath.'-item.component.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-item/'.$templatesPath.'-item.component.ts',
                'template_path'=>'crud/templates/item.component.ts.tpl.php',
            ],
            $templatesPath.'-item.component.scss'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-item/'.$templatesPath.'-item.component.scss',
                'template_path'=>'crud/templates/item.component.scss.tpl.php',
            ],
            //list folder
            $templatesPath.'-list-secure.service.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-list/'.$templatesPath.'-list-secure.service.ts',
                'template_path'=>'crud/templates/list-secure.service.ts.tpl.php',
            ],
            $templatesPath.'-list.component.html'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-list/'.$templatesPath.'-list.component.html',
                'template_path'=>'crud/templates/list.component.html.tpl.php',
            ],
            $templatesPath.'-list.component.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-list/'.$templatesPath.'-list.component.ts',
                'template_path'=>'crud/templates/list.component.ts.tpl.php',
            ],
            $templatesPath.'-list.component.scss'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-list/'.$templatesPath.'-list.component.scss',
                'template_path'=>'crud/templates/list.component.scss.tpl.php',
            ],
            //show folder
            $templatesPath.'-show-secure.service.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-show/'.$templatesPath.'-show-secure.service.ts',
                'template_path'=>'crud/templates/show-secure.service.ts.tpl.php',
            ],
            $templatesPath.'-show.component.html'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-show/'.$templatesPath.'-show.component.html',
                'template_path'=>'crud/templates/show.component.html.tpl.php',
            ],
            $templatesPath.'-show.component.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-show/'.$templatesPath.'-show.component.ts',
                'template_path'=>'crud/templates/show.component.ts.tpl.php',
            ],
            $templatesPath.'-show.component.scss'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-show/'.$templatesPath.'-show.component.scss',
                'template_path'=>'crud/templates/show.component.scss.tpl.php',
            ],
            //new folder
            $templatesPath.'-new-secure.service.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-new/'.$templatesPath.'-new-secure.service.ts',
                'template_path'=>'crud/templates/new-secure.service.ts.tpl.php',
            ],
            $templatesPath.'-new.component.html'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-new/'.$templatesPath.'-new.component.html',
                'template_path'=>'crud/templates/new.component.html.tpl.php',
            ],
            $templatesPath.'-new.component.ts'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-new/'.$templatesPath.'-new.component.ts',
                'template_path'=>'crud/templates/new.component.ts.tpl.php',
            ],
            $templatesPath.'-new.component.scss'=>[
                'entity_class_name' => $entityClassDetails->getShortName(),
                'entity_twig_var_singular' => $entityTwigVarSingular,
                'entity_twig_var_plural' => $entityTwigVarPlural,
                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
                'route_name' => $routeName,
                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
                'destination_path'=>'templates/' . $templatesPath . '/' . $templatesPath . '-new/'.$templatesPath.'-new.component.scss',
                'template_path'=>'crud/templates/new.component.scss.tpl.php',
            ],
//            'edit' => [
//                'entity_class_name' => $entityClassDetails->getShortName(),
//                'entity_twig_var_singular' => $entityTwigVarSingular,
//                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
//                'route_name' => $routeName,
//                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
//            ],
//            'index' => [
//                'entity_class_name' => $entityClassDetails->getShortName(),
//                'entity_twig_var_plural' => $entityTwigVarPlural,
//                'entity_twig_var_singular' => $entityTwigVarSingular,
//                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
//                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
//                'route_name' => $routeName,
//            ],
//            'new' => [
//                'entity_class_name' => $entityClassDetails->getShortName(),
//                'route_name' => $routeName,
//                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
//                'entity_twig_var_singular' => $entityTwigVarSingular,
//            ],
//            'show' => [
//                'entity_class_name' => $entityClassDetails->getShortName(),
//                'entity_twig_var_singular' => $entityTwigVarSingular,
//                'entity_identifier' => $entityDoctrineDetails->getIdentifier(),
//                'entity_fields' => $entityDoctrineDetails->getDisplayFields(),
//                'route_name' => $routeName,
//            ],
        ];

        foreach ($templates as $template => $variables) {
            $generator->generateFile(
                    $variables['destination_path'],
                    $variables['template_path'],
                    $variables
            );
        }

        $generator->writeChanges();

        $this->writeSuccessMessage($io);

        $io->text(sprintf('Next: Check your new CRUD by going to <fg=yellow>%s/</>', Str::asRoutePath($controllerClassDetails->getRelativeNameWithoutSuffix())));
    }

    /**
     * {@inheritdoc}
     */
    public function configureDependencies(DependencyBuilder $dependencies) {
        $dependencies->addClassDependency(
                Route::class, 'router'
        );

        $dependencies->addClassDependency(
                AbstractType::class, 'form'
        );

        $dependencies->addClassDependency(
                Validation::class, 'validator'
        );

        $dependencies->addClassDependency(
                TwigBundle::class, 'twig-bundle'
        );

        $dependencies->addClassDependency(
                DoctrineBundle::class, 'orm-pack'
        );

        $dependencies->addClassDependency(
                CsrfTokenManager::class, 'security-csrf'
        );

        $dependencies->addClassDependency(
                ParamConverter::class, 'annotations'
        );
    }

}
