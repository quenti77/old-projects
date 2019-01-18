$(function () {

    var page = 1;
    var max_page = 1;

    function init() {
        $.ajax({
            url: '/account/book/ajax',
            type: 'post',
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
            url: '/account/book/ajax/' + page,
            type: 'post',
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

    $('.paginationContact').on('click', 'li', function(event) {
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

    function updatePagination() {
        $('.paginationContact').each(function() {
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

    $('#tab_menu').on('click', 'li', function(event) {
        event.preventDefault();

        var elem = $(this);

        $('#tab_menu li').each(function() {
            $(this).removeClass('active');
        });
        elem.addClass('active');
        var page = elem.data('page');

        $('section.container div.panel_tab').each(function() {
            var elem = $(this);

            if (elem.data('page') == page) {
                elem.css('display', 'block')
            } else {
                elem.css('display', 'none')
            }
        });
    });

    init();

});
