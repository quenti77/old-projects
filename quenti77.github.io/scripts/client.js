// Gestion globale de l'application
$(function() {

    // Regex de validation du mail
    function validateEmail(email) {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    // SocketIO connection
    var socket = io.connect('http://tchat.haiser.fr:1337/');

    // JQuery elements;
    var $sidebarPanel = $('#sidebar-panel');
    var $sidebarButton = $('.sidebar-button');

    // Les formulaires d'inscription/connexion // je restart discord lol
    var $register = $('#register');
    var $registerButton = $('#registerButton');

    var $connection = $('#connection');
    var $connectionButton = $('#connectionButton');

    var $btnLeave = $('#btnLeave');
    var $message = $('#message');
    var $content = $('#content');

    // Other variable
    var panel = 'user';
    var user = null;

    // Basic function

    //Recherche les informations à afficher
    function switchPanel() {
        //$sidebarPanel.empty();
    }

    function resultUsers(users) {
        for (var i in users) {
            if (users.hasOwnProperty(i)) {
                var user = users[i];
            }
        }
    }

    function resultChannels(channels) {
        for (var i in channels) {
            if (channels.hasOwnProperty(i)) {
                var channel = channels[i];
            }
        }
    }

    // Quand on change de panel via les boutons
    $sidebarButton.on('click', function(event) {
        event.preventDefault();

        var $buttonPressed = $(this);
        var panelSelected = $buttonPressed.data('panel');

        // Seulement si on choisi le bouton pour changer de panel
        if (panelSelected && panel != panelSelected) {
            $sidebarButton.removeClass('sidebar-active');
            $buttonPressed.addClass('sidebar-active');

            panel = panelSelected;
            switchPanel();
        }
    });

    $register.on('submit', function (event) {
        event.preventDefault();

        var $email = $('#rEmail');
        $email.removeClass('input-failed');

        if (!validateEmail($email.val())) {
            $email.addClass('input-failed');
            $email.focus();

            return false;
        }

        socket.emit('registerUser', {
            nickname: $('#rNickname').val(),
            password: $('#rPassword').val(),
            email: $email.val()
        });
    });

    $connection.on('submit', function (event) {
        event.preventDefault();

        socket.emit('connectionUser', {
            nickname: $('#cNickname').val(),
            password: $('#cPassword').val()
        });
    });

    // Bouton de déconnexion
    $btnLeave.on('click', function (event) {
        event.preventDefault();

        localStorage.removeItem('nickname');
        localStorage.removeItem('token');
        localStorage.removeItem('expire');

        socket.emit('disconnectUser', user);
    });

    $message.on('keyup', function (event) {
        event.preventDefault();

        if (event.keyCode === 13 && $message.val() != '') {
            var msg = $message.val();
            $message.val('');
        }
    });

    // Affichage du message
    function showMessage(data, successAction) {
        var $topMessage = $('#topMessage');
        $topMessage.fadeIn(200);
        $topMessage.removeClass('top-message-success top-message-danger');

        if (data.success) {
            $topMessage.addClass('top-message-success');
            successAction();
        } else {
            $topMessage.addClass('top-message-danger');
        }

        data.message = data.message || '';
        $topMessage.html(data.message);
        $topMessage.removeClass('hidden-form');

        setTimeout(function () {
            $topMessage.fadeOut(200, function() {
                $topMessage.removeClass('top-message-success top-message-danger');
                $topMessage.addClass('hidden-form');
            });
        }, 3000);
    }

    // Socket IO
    socket.on('registerResponse', function (data) {
        showMessage(data, function () {
            $register.addClass('hidden-form');
            $connection.removeClass('hidden-form');
        });
    });

    socket.on('connectionResponse', function (data) {
        showMessage(data, function () {
            user = data.user;

            localStorage.setItem('nickname', data.user.nickname);
            localStorage.setItem('token', data.user.token);
            localStorage.setItem('expire', Math.round(new Date().getTime() / 1000));

            $('#top-container').fadeOut(200, function () {
                $btnLeave.removeClass('hidden-form');
                $btnLeave.fadeIn(200);
            });
        });
    });

    socket.on('disconnectionResponse', function (result) {
        window.location.reload();
    });

    switchPanel();

    setTimeout(function () {
        var nickname = localStorage.getItem('nickname');
        var token = localStorage.getItem('token');
        var expire = localStorage.getItem('expire');
        var nowTime = Math.round(new Date().getTime() / 1000);

        if (nickname && token && expire && (nowTime - expire) < (3600 * 12)) {
            socket.emit('connectionTokenUser', {
                nickname: nickname,
                token: token
            });
        }
    }, 200);

});
