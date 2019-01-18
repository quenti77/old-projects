$(function () {

    var page = 1;
    var max_page = 1;
    var search = '';
    var selected = [];

    function init() {
        $.ajax({
            url: '/admin/ajax_users',
            type: 'post',
            data: {
                research: search
            },
            dataType: 'html',
            success: function(data) {
                if (data == '') {
                    return;
                }

                $('#users').html(data);

                max_page = $('#maxPage').val();
                $('#maxPage').remove();
                updatePagination();
            }
        });
    }

    function back() {
        page -= 1;
        if (page < 1) {
            page = 1;
        }

        changePage();
    }

    function next() {
        page += 1;
        if (page > max_page) {
            page = max_page;
        }

        changePage();
    }

    function changePage() {
        $.ajax({
            url: '/admin/ajax_users/' + page,
            type: 'post',
            data: {
                research: search
            },
            dataType: 'html',
            success: function(data) {
                if (data == '') {
                    return;
                }

                $('#users').html(data);

                max_page = $('#maxPage').val();
                $('#maxPage').remove();
                updatePagination();
            }
        });
    }

    $('.pagination').on('click', 'li', function(event) {
        event.preventDefault();

        var s = $(this).data('id');
        if (s == 'b') {
            back();
        } else if (s == 'n') {
            next();
        } else if (parseInt(s) > 0 && parseInt(s) <= max_page) {
            page = parseInt(s);
            changePage();
        }
    });

    $('#research').on('keyup', function() {
        var research = $(this).val();
        page = 1;
        if (research.length > 1) {
            search = research;
        }else {
            search ='';
        }
        changePage();
    });

    $('#users').on('click', 'tr', function(event) {
        var id = $(this).data('id');
        if (id == undefined) {
            return;
        }

        var index = selected.indexOf(id);
        var elem  = $(this);
        if (index == -1) {
            var urlTarget = '/admin/ajax_user/' + id;

            $.ajax({
                url: urlTarget,
                type: 'post',
                dataType: 'html',
                success: function(data) {
                    if (data == '') {
                        return;
                    }
                    selected.push(id);

                    var newElem = $('<tr class="show-user-' + id +
                    '"><td colspan="6" class="success">' + data + '</td></tr>');
                    elem.closest('tr').after(newElem);
                }
            });
        } else {
            selected.splice(index, 1);
            $('.show-user-' + id + '').remove();
        }
    });

    function updatePagination() {
        $('.pagination').each(function() {
            $(this).html('');
            $('<li data-id="b"><a href="#">&Lt;</a></li>').appendTo(this);

            for (var i = 1; i <= max_page; i += 1) {
                var selected = '';
                if (i == page) {
                    selected = ' class="active"';
                }

                $('<li' + selected + ' data-id="' + i + '"><a href="#">' + i + '</a></li>').appendTo(this);
            }

            $('<li data-id="n"><a href="#">&Gt;</a></li>').appendTo(this);
        });
    }

    init();

});
