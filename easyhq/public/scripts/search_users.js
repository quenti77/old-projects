$(function () {

    var page = 1;
    var max_page = 1;
    var search = '';

    function init() {
        $.ajax({
            url: '/users/ajax',
            type: 'post',
            data: {
                research: search
            },
            dataType: 'html',
            success: function(data) {
                if (data == '') {
                    return;
                }

                $('#resultUsers').html(data);

                max_page = $('#maxUsersPage').val();
                $('#maxUsersPage').remove();
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
            url: '/users/ajax/' + page,
            type: 'post',
            data: {
                research: search
            },
            dataType: 'html',
            success: function(data) {
                if (data == '') {
                    return;
                }

                $('#resultUsers').html(data);

                max_page = $('#maxUsersPage').val();
                $('#maxUsersPage').remove();
                updatePagination();
            }
        });
    }

    $('.paginationUsers').on('click', 'li', function(event) {
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

    $('#researchUsers').on('keyup', function() {
        var research = $(this).val();
        page = 1;
        if (research.length > 1) {
            search = research;
        }else {
            search = '';
        }
        changePage();
    });

    function updatePagination() {
        $('.paginationUsers').each(function() {
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
