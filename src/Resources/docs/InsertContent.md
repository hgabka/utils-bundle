# Insert Content

There is the `Hgabka\KunstmaanExtensionBundle\Entity\PageParts\InsertPagePagePart` Kunstmaan Page Part. Set it in the `main.yml` or in the other page settings yml file:

```yml
name: Main
context: main
types:
    - { name: Audio, class: BssOil\PublicBundle\Entity\PageParts\AudioPagePart }
    # [...]
    - { name: Insert page, class: Hgabka\KunstmaanExtensionBundle\Entity\PageParts\InsertPagePagePart }
```

Now you can insert a page into the other page.
