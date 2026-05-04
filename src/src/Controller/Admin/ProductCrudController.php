<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Product;
use App\Infrastructure\Doctrine\Repository\ProductDoctrineRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

/**
 * CRUD контроллер для товаров
 */
#[AdminRoute]
#[IsGranted('ROLE_ADMIN')]
class ProductCrudController extends AbstractCrudController
{
    public function __construct(
        private ProductDoctrineRepository $productRepo,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    /**
     * Настройка CRUD
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Товар')
            ->setEntityLabelInPlural('Товары')
            ->setSearchFields(['name', 'description'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityPermission('ROLE_ADMIN')
            ->showEntityActionsInlined();
    }

    /**
     * Настройка действий
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

        yield NumberField::new('price', 'Цена (₽)')
            ->setRequired(true)
            ->setNumDecimals(2);

        yield NumberField::new('stock', 'Остаток на складе')
            ->setRequired(true)
            ->setFormTypeOptions([
                'constraints' => [
                    new GreaterThanOrEqual(['value' => 0]),
                ],
            ]);

        yield BooleanField::new('active', 'Активен')
            ->setRequired(true)
            ->renderAsSwitch(true);

        yield AssociationField::new('category', 'Категория')
            ->setRequired(true)
            ->setFormTypeOptions([
                'choice_label' => 'name',
                'placeholder' => 'Выберите категорию',
            ]);
    }
}
