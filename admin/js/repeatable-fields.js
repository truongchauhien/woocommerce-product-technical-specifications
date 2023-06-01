jQuery('.wpts-specification-delete').on('click', function() {
    jQuery(this).closest('.wpts-specification').remove();
});

jQuery('.wpts-specification-add-button').on('click', function() {
    let template = jQuery('.wpts-specification-template').clone(true);
    jQuery(template).attr('class', 'wpts-specification');
    jQuery(template).find('input').first().attr('name', 'wpts-specification-name[]');
    jQuery(template).find('input').eq(1).attr('name', 'wpts-specification-value[]');

    jQuery('.wpts-specification-table').children('tbody').append(template);
    return false;
});

jQuery('.wpts-specification-table')
.children('tbody')
.sortable({

});
