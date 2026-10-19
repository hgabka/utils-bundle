# hgabka/utils-bundle

Általános segédeszköz-gyűjtemény Symfony (7/8) projektekhez: Doctrine kiegészítések, Sonata admin bővítések, AdminList keretrendszer, export, menü/breadcrumb, ACL-es jogosultságkezelés, backend user kezelés, űrlaptípusok, Twig bővítések és egyéb helperek. A többi `hgabka/*` bundle (`node-bundle`, `pagepart-bundle`, `media-bundle`, `simple-content-bundle`, stb.) erre épít.

## Telepítés

```bash
composer require hgabka/utils-bundle
```

```php
// config/bundles.php
Hgabka\UtilsBundle\HgabkaUtilsBundle::class => ['all' => true],
```

Főbb függőségek: `hgabka/doctrine-translatable-bundle`, `liip/imagine-bundle`, `pagerfanta`, `phpoffice/phpspreadsheet`, `google/apiclient`, `symfony/webpack-encore-bundle`. A bundle a konténerben elvárja a `requiredlocales` (`hu|en` formátumú) és a `defaultlocale` paramétereket, ezekből képzi a `hgabka_utils.available_locales`, `hgabka_utils.admin_locales` és `hgabka_utils.default_admin_locale` paramétereket.

## Konfiguráció

```yaml
# config/packages/hgabka_utils.yaml
hgabka_utils:
    recaptcha:
        site_key: '%env(RECAPTCHA_SITE_KEY)%'
        secret: '%env(RECAPTCHA_SECRET)%'
    google:
        api_key: ~
        client_id: ~
        client_secret: ~
    backend_user_class: App\Entity\User      # alapértelmezés: Hgabka\Entity\AbstractUser
    public_access_role: PUBLIC_ACCESS        # alapértelmezés
    admin_firewall_name: admin               # alapértelmezés
```

A bundle `prepend`-del a Twig konfigurációba (űrlap-téma: `@HgabkaUtils/Form/fields.html.twig`), és a Doctrine konfigurációba is bejegyzi a saját hydratorokat és DQL függvényeket minden entity managerhez. A `LongblobType` DBAL típust a bundle `boot()`-ja regisztrálja.

## Mit tartalmaz?

### Doctrine

- **Hydratorok** (`$query->getResult('<név>')`):
  - `key_value` – kulcs → érték tömb (első két oszlop)
  - `column` – egyetlen oszlop értékei lapos tömbben
  - `count` – szám visszaadása
  - `indexed` – az eredménysorok az első oszlop értéke szerint indexelve (`fetchAllAssociativeIndexed`)
- **DQL függvények**: `CAST`, `RAND`, `IF`, `IFNULL`, `REPEAT`, `INSTR`, `DATE`, `ROUND`, `FORMAT`, `REGEXP`, `SUBSTR`, `LEFT`, `CHAR_LENGTH`, `FIRST`, `YEAR`, `MONTH`, `DAY`.
- **Típusok**: `LongblobType`; `AbstractEnumType` + `EnumTypeInterface` – PHP backed enum tárolása Doctrine típusként (a `EnumTypeInterface`-t megvalósító osztályok automatikusan megkapják a `hg_utils.doctrine_enum_type` taget). Az `Enums/TranslatableEnumInterface` és a `Form\Type\TranslatedEnumType` fordítható enum-címkékhez való.
- **Entity traitek / ősök**: `TimestampableEntity` (`createdAt`/`updatedAt`), `TranslatableTrait` (lásd lent), `NestedTreeFieldsTrait` (Gedmo nested set mezők) és `AbstractNestedTreeEntity`, `ArrayAdminTrait`, `BreadcrumbTrait`; `DeepCloneInterface` + `CloneListener`/`CloneHelper` entitások mély klónozásához.

### Fordítható entitások

A `Hgabka\UtilsBundle\Traits\TranslatableTrait` a [`hgabka/doctrine-translatable`](../doctrine-translatable) trait-jét egészíti ki a `translate($locale = null)` metódussal, ami a megadott (vagy aktuális) nyelvű fordítás-objektumot adja vissza, és ha még nincs ilyen, létrehozza.

```php
#[ORM\Entity]
class Article implements TranslatableInterface
{
    use TranslatableTrait;

    #[Hgabka\Translations(targetEntity: ArticleTranslation::class)]
    private Collection|array|null $translations = null;
    // ...
}
```

### Sonata admin

- `Admin\AbstractSortableAdmin` + `Controller\SortableCRUDController` – kézzel sorbarendezhető listák (`SortableInterface`).
- `Admin\NestedTreeAdmin` + `Controller\NestedTreeCRUDController` – fa struktúrájú entitások adminja (`toStringInTree`, `getSubFormBuilder`).
- `Controller\UrlChooserCRUDController` – URL-választó popup a szerkesztőkhöz.
- `Query\CustomSortProxyQuery`, `Query\ArrayAdminProxyQuery` – egyedi rendezés / tömb alapú lista (lásd lentebb).
- `Form\Type\*Autocomplete*`, `Form\Type\DatepickerType`, `DateTimepickerType`, `DatepickerRangeType`, `NumberRangeType`, `ColorType`, `RangeType`, `LocaleType`, `DynamicCollectionType`, `StaticControlType`, `RecaptchaType`, `InvisibleRecaptchaType`, `Form\WysiwygType`, `Form\GroupType`, `Form\RoleType` – űrlaptípusok. A `Filter\NumberRangeFilter` Sonata szűrő.

### AdminList

Saját, Sonata-független lista keretrendszer (`AdminList\AdminList`, `AdminListFactory`, `Configurator\AbstractDoctrineORMAdminListConfigurator`, `AbstractDoctrineDBALAdminListConfigurator`). Támogatja a szűrőket (`FilterType\ORM\{String,Number,Boolean,Date,DateTime,Enumeration}FilterType`), item/list/bulk actionöket, lapozást (Pagerfanta) és exportot (`ExportableInterface`, `ExportListConfiguratorInterface`). Twig oldalról az `adminlist_widget()` függvénnyel renderelhető.

### Export

`Export\AdminExporter` és `Export\EntityExporter` CSV (`CsvWriter`), XLSX (`XlsxWriter`, PhpSpreadsheet) és XML (`XmlWriter`) formátumba. A támogatott kiterjesztések Twigből: `supported_export_extensions()`.

### Backend user és biztonság

- `Model\AbstractUser`, `UserInterface`, `UserManager`, `Security\UserProvider`, `UserChecker`, `LoginManager`, jelszó hasherek (`Md5PasswordHasher`, `Sha1PasswordHasher`), `Util\PasswordUpdater`, `TokenGenerator`, `UserManipulator`.
- `Controller\AbstractLoginController`, `Form\AdminLoginForm`.
- Google OAuth belépés segédei: `Helper\Security\OAuth\OAuthUserCreator`, `OAuthUserFinder`.
- ACL: `Helper\Security\Acl\AclHelper`, `AclNativeHelper` (ACL-szűrt lekérdezések), `AclVoter`, `Permission\PermissionMap`, `MaskBuilder`, `PermissionAdmin`, `Entity\AclChangeset`.
- Konzol parancsok:

| Parancs | Leírás |
|---|---|
| `hgabka:backend-user:create` | backend user létrehozása |
| `hgabka:backend-user:promote` / `demote` | szerep hozzáadása / elvétele |
| `hgabka:user:promote` / `demote` | ugyanez általános usereknek |
| `fos:user:activate` / `deactivate` / `change-password` | user aktiválása, deaktiválása, jelszócsere |
| `hgabka:acl:apply` | függő ACL changesetek alkalmazása |
| `hgabka:acl:update` | egy szerep ACL jogainak frissítése az összes node-ra |

Az `AbstractLockableCommand` egyszerre csak egy példányban futó parancsokhoz használható ősosztály.

### Menü, breadcrumb, admin panel

- `Helper\Menu\MenuBuilder` + `MenuAdaptorInterface` (`SimpleMenuAdaptor`, `ModulesMenuAdaptor`) – adaptor alapú admin menüépítés, Twigben `get_admin_menu()`.
- `Breadcrumb\BreadcrumbManager` – morzsamenü (`add()`, `addCurrentRoute()`, `addHomePage()`, `trim()`, `clear()`).
- `Helper\AdminPanel` – admin panel akciók (`get_admin_panel_actions()`).
- `Helper\FormWidgets` (`Tab`, `TabPane`, `ListWidget`) – fülek és widgetek az űrlapokon (`tabs_widget()`).

### Twig

| Függvény / szűrő | Mit csinál |
|---|---|
| `slugify`, `hg_spaceless`, `localeDate`, `utils_format_number`, `utils_format_price` | szűrők |
| `get_locales`, `get_backend_locales`, `locale_display_name`, `localeswitcher_widget` | nyelvek, nyelvváltó |
| `adminlist_widget`, `permissionsadmin_widget`, `tabs_widget` | widgetek |
| `form_errors_recursive`, `form_has_errors_recursive` | űrlaphibák |
| `encore_entry_link_tags_advanced` | Webpack Encore bővített entry tagek (`Asset\TagRenderer`) |
| `get_admin_menu`, `get_admin_panel_actions`, `hideSidebarInNodeEditAdmin` | admin felület |

### Egyéb helperek

- `Helper\HgabkaUtils` (`hgabka_utils` szolgáltatás) – nyelvkezelés (`getCurrentLocale`, `getAvailableLocales`, `getAdminLocale`, `getLocaleChoices`), slugify, `entityToArray`/`entityFromArray`, string/tömb/dátum/fájlrendszer segédek, római számok, dátumperiódus, `truncateToNearestWord`, stb.
- `Helper\Slugifier`, `Formatter`, `Number\NumberToText` / `Words` (számok szöveggé alakítása sok nyelven, köztük `hu`), `Cipher` / `UrlSafeCipher`, `Shell`, `ClassLookup`, `DomainConfiguration`, `RequestHelper`, `HttpCache\Manager`, `Google\DriveDownloader`, `Util\HtmlToText`.
- Validátorok: `Recaptcha`, `TaxId`, `TaxNo` (adószám), `Barcode\BarcodeValidator`.
- Imagine szűrők: `Fill`, `Fit`, `Paste` (LiipImagine).
- `FlashMessages\FlashTypes`, `Choices\ConstantsChoiceList`, `Enums\ConstantsChoiceLoader`.

### Frontend

Az `assets/` könyvtár SCSS/CSS és JS erőforrásokat tartalmaz (alertify, datetimepicker, colorpicker, select2, jstree, ajax modal). Az AJAX-os typeahead leírása: [Readme.typeahead.md](Readme.typeahead.md).

---

## Egyedi rendezés Sonata listákban

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
