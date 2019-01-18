$(function () {

    var search = '';
    var page = 1;
    var max_page = 1;
    var id_project = 0;

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
            url: '/tasks/details/ajax/' + id_project + '-' + page,
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

    $('#client').on('keyup', function() {
        var search = $(this).val();

        if (search.length > 2) {
            setSearch(search);
        } else {
            resetSearch();
        }
    });

    function resetSearch() {
        var res = $('#result_client');
        res.css('display', 'none');
    }

    function setSearch(research_value) {
        search = research_value;
        page = 1;

        $.ajax({
            url: '/tasks/details/ajax/' + id_project,
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

        var res = $('#result_client');
        res.css('display', 'block');
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

    resetSearch();
    updatePagination();

    id_project = $('#id_project_book').val();
    $('#id_project_book').remove();

});
