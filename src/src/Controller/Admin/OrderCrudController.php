<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Domain\Entity\Order;
use App\Domain\Entity\OrderStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\TextFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * CRUD контроллер для заказов
 *
 * @extends AbstractCrudController<Order>
 */
#[AdminRoute]
#[IsGranted('ROLE_ADMIN')]
final class OrderCrudController extends AbstractCrudController
{
    /**
     * @return array<string, string>
     */
    private static function statusChoices(): array
    {
        $choices = [];
        foreach (OrderStatus::cases() as $status) {
            $choices[$status->label()] = $status->value;
        }
        return $choices;
    }

    /**
     * @return class-string<Order>
     */
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    /**
     * Настройка CRUD
     *
     * @return Crud
     */
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Заказ')
            ->setEntityLabelInPlural('Заказы')
            ->setSearchFields(['orderNumber', 'customerName', 'customerEmail'])
            ->setDefaultSort(['id' => 'DESC'])
            ->setEntityPermission('ROLE_ADMIN')
            ->setPaginatorPageSize(20)
            ->showEntityActionsInlined()
            ->setDateFormat('dd.MM.yy')
            ->setTimeFormat('HH:mm')
            ->setDateTimeFormat('dd.MM.yy, HH:mm')
            ->setTimezone('Europe/Moscow');
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
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn (Action $action) => $action->setLabel('Изменить статус'))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn (Action $action) => $action->setLabel('Удалить'));
    }

    /**
     * Настройка фильтров
     *
     * @return Filters
     */
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(TextFilter::new('orderNumber'))
            ->add(TextFilter::new('customerEmail'))
            ->add(ChoiceFilter::new('status')
                ->setChoices(self::statusChoices()));
    }

    /**
     * Настройка полей
     */
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();

        yield TextField::new('orderNumber', 'Номер заказа')
            ->setDisabled(true)
            ->hideOnForm();

        yield TextField::new('customerName', 'Имя клиента')
            ->setDisabled(true);

        yield EmailField::new('customerEmail', 'Email')
            ->setDisabled(true);

        yield TextField::new('customerPhone', 'Телефон')
            ->setDisabled(true);

        yield TextField::new('deliveryAddress', 'Адрес доставки')
            ->setDisabled(true)
            ->hideOnIndex();

        yield ChoiceField::new('status', 'Статус')
            ->setChoices(self::statusChoices())
            ->setRequired(true)
            ->renderAsBadges([
                'pending' => 'warning',
                'paid' => 'info',
                'confirmed' => 'primary',
                'shipped' => 'success',
                'delivered' => 'success',
                'cancelled' => 'danger',
            ]);

        yield NumberField::new('totalAmount', 'Сумма (₽)')
            ->setDisabled(true)
            ->setNumDecimals(2);

        yield DateTimeField::new('createdAt', 'Дата создания')
            ->setDisabled(true)
            ->hideOnForm();

        // Элементы заказа (только просмотр)
        if ($pageName === Crud::PAGE_DETAIL || $pageName === Crud::PAGE_EDIT) {
            yield CollectionField::new('items', 'Товары')
                ->hideOnForm()
                ->onlyOnDetail()
                ->setTemplatePath('admin/collection/order_items.html.twig');
        }
    }
}
