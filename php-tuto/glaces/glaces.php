<?php

define('BOULES', 3);
define('PARFUM', 3);

$parfums = ['pistache', 'fraise', 'chocolat', 'vanille', 'cafe', 'menthe'];

$glaces = [];
$glace = [];

for ($i = 0; $i < BOULES; $i += 1) {
    $glace[$i] = 1;
}

function printArray($tab) {
    return implode(",", $tab);
}

// La première ligne

function addPosition($glace, $position) {
    if ($position < 0) return [$glace, $position];

    $glace[$position] += 1;
    if ($glace[$position] > PARFUM) {
        $glace[$position] = 1;
        return addPosition($glace, $position - 1);
    }

    return [$glace, $position];
}

function doublon($glaceActuel, $glaces) {
    sort($glaceActuel);
    foreach ($glaces as $glace) {
        sort($glace);

        if ($glace == $glaceActuel) {
            return true;
        }
    }
    return false;
}

$position = BOULES;
while ($position >= 0) {
    $position = BOULES - 1;
    for ($i = 1; $i <= PARFUM; $i += 1) {
        $glace[$position] = $i;
        if (!doublon($glace, $glaces)) {
            $glaces[] = $glace;
        }
    }

    $result = addPosition($glace, $position - 1);
    $glace = $result[0];
    $position = $result[1];
}

?>
<html>
<head>
    <style>
        .boule {
            display: inline-block;
            color: #e1e1e1;
            font-size: 1.4em;

            width: 48px;
            height: 48px;
            margin-left: 2px;
            margin-right: 2px;

            border-radius: 50%;

            line-height: 48px;
            text-align: center;
        }

        .pistache {
            box-shadow: inset 0 0 1px 3px #185c00;
            background-color: #228a00;
        }
        .fraise {
            box-shadow: inset 0 0 1px 3px #753176;
            background-color: #b943ba;
        }
        .chocolat {
            box-shadow: inset 0 0 1px 3px #1f0b0b;
            background-color: #3a1212;
        }
        .vanille {
            box-shadow: inset 0 0 1px 3px #aea72d;
            background-color: #d2cb37;
        }
        .cafe {
            box-shadow: inset 0 0 1px 3px #3b2109;
            background-color: #55370a;
        }
        .menthe {
            box-shadow: inset 0 0 1px 3px #2865a3;
            background-color: #2a82ca;
        }
    </style>
</head>
<body>
<?php $count = 0; foreach ($glaces as $glace): ?>
    <?php $count += 1; $countLine = sprintf("%'.02d <br>", $count) ?>

    <?php foreach ($glace as $boule): ?>
        <div class="boule <?= $parfums[$boule - 1] ?>">
            <?= $boule ?>
        </div>
    <?php endforeach; ?>
    <?= $countLine ?><br>
<?php endforeach; ?>
</body>
</html>
