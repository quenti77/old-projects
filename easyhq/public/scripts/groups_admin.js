$(function () {

    var selected = [];

    $('#groups').on('click', 'tr', function(event) {
        var id = $(this).data('id');
        if (id == undefined) {
            return;
        }

        var index = selected.indexOf(id);
        var elem  = $(this);
        if (index == -1) {
            var urlTarget = '/admin/ajax_group/' + id;
            if (id == 0) {
                urlTarget = '/admin/ajax_group';
            }

            $.ajax({
                url: urlTarget,
                type: 'post',
                dataType: 'html',
                success: function(data) {
                    if (data == '') {
                        return;
                    }
                    selected.push(id);

                    var newElem = $('<tr class="show-group-' + id +
                                    '"><td colspan="6" class="success">' + data + '</td></tr>');
                    elem.closest('tr').after(newElem);
                }
            });
        } else {
            selected.splice(index, 1);
            $('.show-group-' + id + '').remove();
        }
    });

});