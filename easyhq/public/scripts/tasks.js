$(function () {

    var selected = [];

    $('#tasktable').on('click', 'tr', function(e) {
        e.stopPropagation();

        var nodeName = e.target.nodeName;
        if (nodeName != 'TD' && nodeName != 'STRONG') {
            return;
        }

        var id = $(this).data('id_task');
        if (id == undefined) {
            return;
        }

        var index = selected.indexOf(id);
        var elem  = $(this);
        if (index == -1) {
            var urlTarget = '/tasks/details/ajax/updatetask/' + id;
            if (id == 0) {
                urlTarget = '/tasks/details/ajax/inserttask';
            }

            $.ajax({
                url: urlTarget,
                type: 'post',
                data: {
                    id_project: elem.data('id_project')
                },
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


    $('.task').on('click','input',isDone);
    function isDone(){
        var line = $(this).parent().parent();
        var id_task = line.data('id_task');
        var id_project = line.data('id_project');
        var completeValue = $(this).is(':checked');
        $.ajax({
            url:'/tasks/details/ajax/completetask',
            type:'post',
            data: {
                id : id_task,
                id_p : id_project,
                complete : completeValue
            },
            dataType: 'json',
            success:function(data){
                if (data != undefined && data.success != undefined) {
                    var beforeClass = 'progress-bar-danger progress-bar-warning progress-bar-success';
                    $('#pbar').removeClass(beforeClass);
                    var name = 'danger';
                    var pb = data.progress[0];
                    var pourcentage = parseFloat(pb.progress).toFixed(2);

                    $('#progress').html(pourcentage+'%');
                    $('#nb_complete').html(pb.nb_finished+'/'+pb.nb_tasks);
                    if (pourcentage >= 20.0 && pourcentage < 50.0) {
                        name = 'warning';
                    } else if (pourcentage >= 50.0) {
                        name = 'success';
                    }
                    $('#pbar').addClass('progress-bar-'+name)
                        .css('width', parseInt(pb.progress) + '%');
                    if (completeValue == true){
                        line.addClass('success');
                    }else {
                        line.removeClass('success');
                    }
                }
            }
        });
    }
});