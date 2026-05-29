<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * CRUD контроллер для категорий
 *
 * @extends AbstractCrudController<Category>
 */
#[AdminRoute]
#[IsGranted('ROLE_ADMIN')]
final class CategoryCrudController extends AbstractCrudController
{
    /**
     * @return class-string<Category>
     */
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    /**
     * Настройка CRUD
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Категорию')
            ->setEntityLabelInPlural('Категории')
            ->setSearchFields(['name', 'description'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityPermission('ROLE_ADMIN')
            ->showEntityActionsInlined();
    }

    /**
     * Настройка действий
     *
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->update(Crud::PAGE_INDEX, Action::NEW, fn (Action $action) => $action->setIcon('fa fa-plus'))
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) => $action->setIcon('fa fa-edit'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $action) => $action->setIcon('fa fa-trash'));
    }

    /**
     * Настройка фильтров
     *
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('name'))
            ->add(BooleanFilter::new('active'));
    }

    /**
     * Настройка полей
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('name', 'Название')
            ->setRequired(true)
            ->setMaxLength(200);

        yield TextField::new('description', 'Описание')
            ->setRequired(false)
            ->hideOnIndex()
            ->setMaxLength(1000)
            ->onlyOnForms();

        yield BooleanField::new('active', 'Активна')
            ->setRequired(true)
            ->renderAsSwitch(true);
    }
}
