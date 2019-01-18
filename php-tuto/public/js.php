<?php

// nickname---password
// Chiyome---$2y$10$/GSnAqa2MV6IjQUNmOBzNOXjvr1m52.ujf6B1OMBA.ninDzrMapwS
// 142058678126772224

?>

<html>
<head>
    <meta charset="UTF-8">
    <title>JS finder</title>
</head>
<body>
<div id="result">
    <!-- Nothing -->
</div>

<script src="https://code.jquery.com/jquery-2.2.4.js" integrity="sha256-iT6Q9iMJYuQiMWNd9lDyBUStIq/8PuOW33aOqmvFpqI=" crossorigin="anonymous"></script>
<script>
    $(function() {
        var letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789&é\"'(-è_çà)=$^*ù!:;,<>?./§%µ£¨+°²".split('');
        var word = [];
        word[0] = letters[0];
        word[1] = word[0];
        word[2] = word[0];
        word[3] = word[0];
        word[4] = word[0];
        word[5] = word[0];
        word[6] = word[0];
        word[7] = word[0];

        function addLetter(position) {
            if (word[position] == letters[letters.length - 1]) {
                word[position] = letters[position];

                addLetter(position + 2);
            } else {
                word[position] = letters[letters.indexOf(word[position]) + 1];
            }
        }

        function check() {
            $.post('/ajax.php', { password: word.join('') }, function (result) {
                var value = 'PWD: ' + result.password + '<br>Success : ' + (result.success ? 'TRUE' : 'FALSE');
                $('#result').html(value);

                if (!result.success) {
                    if (word[0] == letters[letters.length - 1]) {
                        word[0] = letters[0];

                        addLetter(1);
                    } else {
                        word[0] = letters[letters.indexOf(word[0]) + 1];
                    }

//                    window.setTimeout(function() {
//
//                    }, 8);
                    check();
                }
            });
        }

        check();
    });
</script>
</body>
</html>
