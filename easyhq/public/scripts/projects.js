$(function () {

    var selected = [];

    $('#projects').on('click', 'tr', function(e) {
        e.stopPropagation();

        var nodeName = e.target.nodeName;
        if (nodeName != 'TD' && nodeName != 'STRONG') {
            return;
        }

        var id = $(this).data('id');
        if (id == undefined) {
            return;
        }

        var index = selected.indexOf(id);
        var elem  = $(this);
        if (index == -1) {
            var urlTarget = '/tasks/project/ajax/' + id;
            if (id == 0) {
                urlTarget = '/tasks/project/ajax';
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

                    var newElem = $('<tr class="show-project-' + id +
                    '"><td colspan="6" class="success">' + data + '</td></tr>');
                    elem.closest('tr').after(newElem);
                }
            });
        } else {
            selected.splice(index, 1);
            $('.show-project-' + id + '').remove();
        }
    });

});