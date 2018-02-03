function addAutocompleteItem(item, $form, field)
{
    var exists = false;
    var currentIndex = $form.data('index');

    var length = $form.find('.tag').length;
    var maxItems = $form.data('maximum-items');

    if (maxItems != '' && length >= maxItems )
    {
      alert('Több elem nem adható hozzá!');

      return;
    }

    $form.find('.tag').find('input[type="hidden"]').each(function()
         {
            if ($(this).val() == item.value)
            {
                exists = true;
            }
        }
    );

    if (exists)
    {
        alert('Az elem már a listában van!');

        return;
    }

    var prototype = $form.data('prototype');
    var nestedLevelProtoName = prototype.match(/__[a-z]+__/g)[0];
    var regExpName = new RegExp(nestedLevelProtoName, 'g');

    prototype = prototype.replace(regExpName, currentIndex);

    var $newItem = $(prototype);

    $newItem.find('input[type="hidden"]').val(item.value);
    var label = item.title == null ? item.label : item.title;
    $newItem.find('span.tag-label').html(label);

    $form.append($newItem);
    $form.data('index', currentIndex + 1);

    $(field).val('');
}
