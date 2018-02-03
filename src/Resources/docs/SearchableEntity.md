# Kereshető entity

Az entity-nek meg kell valósítania a `\Webtown\KunstmaanExtensionBundle\Entity\SearchableEntityInterface`-t. A következő adatokat kell megadni:

## getSearchTitle

Ez az oldal címe. Ne tartalmazzon HTML tag-et, erre nem fog lefutni a tagmentesítő szűrő! Nyelvenként eltérő választ adjon.

## getSearchContent

Ez a tartalom, amiben keres. Ha lehet `\n\n`-nel kapcsold össze a különböző mezőket, amiket szükségesnek tartasz beszúrni:

```php
<?php
class Entity
{
    public function getSearchContent($locale = null)
    {
        return implode("\n\n", [
            $this->getTitle($locale),
            $this->getShortDescription($locale),
            $this->getLongDescrpition($locale),
        ]);
    }
}
```

## getSearchType

A fordítandó kulcsot vár. Ezt aggregációnál használja írja ki:

```
Page (2)
Product (5)
Location (1)
```

## getSearchRouteName + getSearchRouteParameters

Itt kell megadni azt, hogy miként a linkelje a találatot.

## getSearchUniqueEntityName

Minden kereső dokumentumhoz készül egy egyedi ID. Az ID generálásánál használja az itt megadott stringet. Egyedinek kell lennie a rendszerben!
