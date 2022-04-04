# utils-bundle
Utils

Custom Sonata rendezés:
Néha kellhet olyan, hogy egy oszlop header-rel speciális rendezést valósítsunk meg, vagy olyan dolgokat csináljunk, amit a Sonata nem tud.

Ehhez készült egy kiegészítés.

1. Rendezés custom field alapján:

Az admin osztályban felüldefináljuk a configureQuery metódust, abban a szokásos Doctrine-os AS HIDDEN szintakszissal definiáljuk a custom mezőket a query-hez:

```php
use Hgabka\UtilsBundle\Query\CustomSortProxyQuery;

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = $this->getModelManager()->getEntityManager($this->getClass())->createQueryBuilder();

        $queryBuilder
            ->select('o')
            ->from(GuaranteedPresent::class, 'o')
            ->addSelect('IF (
                        (o.validFrom IS NULL OR o.validFrom <= :now)
                        AND (o.validUntil IS NULL OR o.validUntil >= :now), 1, 0) AS HIDDEN sortactive
                        ')
            ->setParameter('now', new DateTime())
        ;

        return new CustomSortProxyQuery($queryBuilder);
    }
```
Fontos, hogy a visszaadott proxy query osztálya a CustomSortProxyQuery legyen.

Majd a configureListFields-ben a sort_field_mapping alatt egy callback-ket definiálunk, ami megkapja a querybuilder-t:

```php
        $list
            ->add('activeReally', FieldDescriptionInterface::TYPE_BOOLEAN, [
                'label' => 'Aktív',
                'sort_field_mapping' => [
                    'fieldName' => static function ($query, $order) {
                        $query->orderBy('sortactive', $order);
                    },
                ],
                'sort_parent_association_mappings' => [],
                'sortable' => true,
            ])


```

2. A query eleve rendezett alapból valami olyan field szerint, ami nem szerepel a listában, de az oszlopnévre kattintva ez felülírható

A configureQuery-t felülírjuk:
```php
    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        $queryBuilder = $this->getModelManager()->getEntityManager($this->getClass())->createQueryBuilder();

        $queryBuilder
            ->select('o')
            ->from(Order::class, 'o')
            ->leftJoin('o.buyer', 'b')
            ->leftJoin('o.items', 'i')
            ->leftJoin('i.productPackaging', 'p')
            ->leftJoin('p.product', 'pr')
            ->leftJoin('pr.brand', 'br')
        ;
        $alias = current($queryBuilder->getRootAliases());
        $statusIn = $query->expr()->in($alias . '.adminStatus', [OrderAdminStatusChoices::ADMIN_STATUS_LATER, OrderAdminStatusChoices::ADMIN_STATUS_TOCALL]);

        $queryBuilder->orderBy('IF (' . $statusIn . ',0,1)');
        $queryBuilder->andWhere($queryBuilder->expr()->in($alias . '.status', [OrderStatusChoices::AVAILABLE, OrderStatusChoices::DONE, OrderStatusChoices::ORDERED]));

        return new CustomSortProxyQuery($queryBuilder);
    }
```

Majd a configureListFields-ben egy tömböt adunk át, aminek van egy 'priority' kulcsa 'high' értékkel.
A high priority azt jelenti, hogy az oszlop szerinti rendezés történik meg először, és csak utána a configureQuery-ben definiált.

```php
         $list
            ->add('totalPriceGross', null, [
                'label' => 'Érték',
                'template' => 'admin/order/list_field_totalPriceGross.html.twig',
                'sort_field_mapping' => [
                    'fieldName' => [
                        'priority' => 'high',
                        'field' => 'totalPriceGross',
                    ],
                ],
            ])


```
Itt tehát először a totalPriceGross mező szerinti a rendezés, utána jön az adminStatus szerinti.
Ha az alapból rendezést minden körülmények között meg kell őrizni, akkor a priority legyen 'low'. Ilyenkor az oszlop szerinti rendezés csak az alap rendezés után történik meg.

Ha nincs megadva priority, akkor az 'low'-nak számít.

3. Ha a kettőt kombinálni kell:

```php
         $list
            ->add('totalPriceGross', null, [
                'label' => 'Érték',
                'template' => 'admin/order/list_field_totalPriceGross.html.twig',
                'sort_field_mapping' => [
                    'fieldName' => [
                        'priority' => 'low',
                        'callback' => static function ($query, $order) {
                            $query->orderBy('sortactive', $order);
                        },,
                    ],
                ],
            ])
```
