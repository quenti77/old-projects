// Gestion globale pour les effet CSS
$(function() {

    // Pour le menu
    var $sidebar = $('#sidebar');
    var $sidebarMenu = $('#sidebar-menu');
    var $sidebarButton = $('.sidebar-button');
    var $userAvatar = $('.user-avatar');
    var $inlineHiddenMobile = $('.inline-hidden-mobile');
    var menuToggle = false;

    function deactivate() {
        // C'est actif donc on le rends inactif
        var $userNickname = $('.user-nickname');

        var $channel = $('.channel');
        var $channelName = $('.channel-name');

        $inlineHiddenMobile.removeClass('force-inline');
        $userAvatar.removeClass('user-avatar-extend');
        $userNickname.removeClass('user-nickname-mobile');
        $channel.removeClass('text-left space-left');
        $channelName.removeClass('force-inline');
        $sidebarButton.removeClass('text-left important-space-left');
        $sidebar.removeClass('middle-width');

        menuToggle = false;
    }

    function activate() {
        // C'est inactif donc on le rends actif
        var $userNickname = $('.user-nickname');

        var $channel = $('.channel');
        var $channelName = $('.channel-name');

        $inlineHiddenMobile.addClass('force-inline');
        $userAvatar.addClass('user-avatar-extend');
        $userNickname.addClass('user-nickname-mobile');
        $channel.addClass('text-left space-left');
        $channelName.addClass('force-inline');
        $sidebarButton.addClass('text-left important-space-left');
        $sidebar.addClass('middle-width');

        menuToggle = true;
    }

    // Pour le menu version mobile quand on clique dessus
    // Système de toggle SHOW/HIDE
    $sidebarMenu.on('click', function(event) {
        event.preventDefault();

        if (menuToggle) {
            deactivate();
        } else {
            activate();
        }
    });

    $(window).resize(function() {
        deactivate();
    });

    $('#content').click(function() {
        deactivate();
    });

    // Les formulaires d'inscription/connexion
    var $register = $('#register');
    var $registerButton = $('#registerButton');

    var $connection = $('#connection');
    var $connectionButton = $('#connectionButton');

    $registerButton.on('click', function (event) {
        event.preventDefault();

        $connection.addClass('hidden-form');
        $register.removeClass('hidden-form');
    });

    $connectionButton.on('click', function (event) {
        event.preventDefault();

        $register.addClass('hidden-form');
        $connection.removeClass('hidden-form');
    });

});