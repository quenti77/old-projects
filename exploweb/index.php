<?php
if (!empty($_POST)) {
    // Quand on appel en ajax notre fichier
    $code = 200;
    $data = [];

    $folder = '~';
    if (!empty($_POST['folder'])) {
        $folder = $_POST['folder'];
    }

    if (strpos($folder, '~/') !== 0) {
        $folder = '~/'.$folder;
    }

    $folder = realpath(__DIR__.'/'.str_replace('~', '.', $folder));
    if (is_dir($folder)) {
        $list = scandir($folder);

        $folders = [];
        $files = [];

        foreach ($list as $element) {
            if (!in_array($element, ['.'])) {
                $path = realpath($folder.'/'.$element);

                if (file_exists($path)) {
                    if (is_dir($path)) {
                        $folders[] = $element;
                    } else {
                        $fileInfo = pathinfo($path);

                        $f = [
                            'name' => $fileInfo['filename'],
                            'type' => (!empty($fileInfo['extension'])) ? $fileInfo['extension'] : null
                        ];

                        if (empty($f['name'])) {
                            $f['name'] = $f['type'];
                        }

                        $files[] = $f;
                    }
                }
            }
        }

        $data['folders'] = $folders;
        $data['files'] = $files;
    } else {
        $code = 400;
        $data['message'] = 'Impossible de lire le dossier "'.$folder.'"';
    }

    header('Content-Type: application/json', true, $code);
    echo json_encode($data, JSON_NUMERIC_CHECK);
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Explorateur de fichier</title>

    <style>
        *, *:before, *:after {
            margin: 0;
            padding: 0;

            box-sizing: border-box;
            text-decoration: none;
        }

        html, body {
            width: 100vw;
            height: 100vh;
        }

        body {
            background-color: #ececec;
            font-family: "Open Sans", Arial, sans-serif;
        }

        .app {
            display: flex;
            flex-direction: column;

            height: 100vh;
        }

        .header, .footer {
            display: flex;
            justify-content: center;

            background-color: #019875;
            padding: 10px 5px;
            color: #ecf0f1;
        }

        .header {
            font-size: 2em;
        }

        .header-path {
            display: flex;
            align-items: stretch;
        }

        .header-path .separator {
            align-self: center;
        }

        .btn {
            margin: 0 10px;
            padding: 4px 14px;

            background-color: #03c9a9;
            color: #ecf0f1;

            border: 2px solid #4ecdc4;
            border-radius: 3px;

            cursor: pointer;
        }

        .btn:hover .can-rotate {
            animation: rotate 1.5s linear infinite;
        }

        .icon-mini {
            width: 24px;
            height: 24px;
        }

        .icon {
            width: 64px;
            height: 64px;
        }

        .container {
            display: flex;
            justify-content: stretch;

            flex: 1;
        }

        .container .panel {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-start;

            flex: 1;
            padding: 10px;
            overflow-y: auto;
        }

        .panel-element {
            display: flex;
            flex-direction: column;
            max-width: 74px;

            margin: 2px 10px;
            padding: 5px;
            text-align: center;
            cursor: pointer;

            transition: background-color 200ms linear;
        }

        .panel-element .icon {
            margin-bottom: 5px;
        }

        .panel-element:hover {
            background-color: #03c9a9;
        }

        .container .panel:first-child {
            border-right: 1px solid
        }

        @keyframes rotate {
            0% {
                transform: rotateZ(0);
            }

            50% {
                transform: rotateZ(-180deg);
            }

            100% {
                transform: rotateZ(-360deg);
            }
        }
    </style>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.2.6/vue.js"></script>
</head>
<body>

<div id="app" class="app">
    <!-- Notre application -->
    <header class="header">
        <button class="btn" @click="refresh()" style="{align-self: flex-start;}">
            <svg class="icon-mini can-rotate" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                 viewBox="0 0 286.052 286.052" style="enable-background:new 0 0 286.052 286.052;" xml:space="preserve">
                <g>
                    <path style="fill:#ecf0f1;" d="M78.493,143.181H62.832v-0.125c0-43.623,34.809-80.328,79.201-80.122
                        c21.642,0.098,41.523,8.841,55.691,23.135l25.843-24.931c-20.864-21.043-49.693-34.049-81.534-34.049
                        c-63.629,0-115.208,51.955-115.298,116.075h-15.84c-9.708,0-13.677,6.49-8.823,14.437l33.799,33.504
                        c6.704,6.704,10.736,6.91,17.646,0l33.799-33.504C92.17,149.662,88.192,143.181,78.493,143.181z M283.978,129.236l-33.799-33.433
                        c-6.892-6.892-11.156-6.481-17.637,0l-33.799,33.433c-4.854,7.929-0.894,14.419,8.814,14.419h15.635
                        c-0.25,43.337-34.943,79.72-79.183,79.514c-21.633-0.089-41.505-8.814-55.691-23.099l-25.843,24.896
                        c20.873,21.007,49.702,33.996,81.534,33.996c63.432,0,114.869-51.579,115.28-115.298h15.867
                        C284.872,143.655,288.832,137.156,283.978,129.236z"></path>
                </g>
            </svg>
        </button>
        <button class="btn" @click="parentFolder()" v-if="path.length < -1">
            <svg class="icon-mini" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 487.189 487.189" style="enable-background:new 0 0 487.189 487.189;" xml:space="preserve" width="512px" height="512px">
                <g>
                    <g>
                        <path d="M470.766,201.645H328.514c-0.679,12.523-5.831,23.854-14.075,32.277h140.188v107.525l-16.752-58.983    c-1.971-6.934-8.307-11.726-15.525-11.726H80.981v-89.762h98.881v-32.277H64.843c-8.92,0-16.139,7.225-16.139,16.139v105.9H16.427    c-5.059,0-9.834,2.371-12.877,6.406c-3.057,4.043-4.033,9.276-2.647,14.146l46.049,162.18    c3.374,11.881,14.224,20.077,26.574,20.075l385.762-0.065c15.253-0.003,27.615-12.368,27.615-27.62V217.783    C486.903,208.871,479.687,201.645,470.766,201.645z" fill="#ecf0f1"></path>
                        <path d="M486.902,487.174v-0.14C486.836,487.162,486.828,487.219,486.902,487.174z" fill="#ecf0f1"></path>
                    </g>
                    <path d="M179.373,101.74h32.766v97.171c0,9.865,7.975,17.848,17.824,17.848h48.699c9.867,0,17.857-7.982,17.857-17.848V101.74   h32.75c5.404,0,10.305-3.27,12.387-8.282c2.064-4.988,0.932-10.756-2.914-14.594L263.783,3.931C261.184,1.314,257.748,0,254.313,0   c-3.42,0-6.855,1.314-9.473,3.931l-74.924,74.934c-3.861,3.838-4.98,9.605-2.898,14.594   C169.098,98.471,173.967,101.74,179.373,101.74z" fill="#ecf0f1"></path>
                </g>
                </svg>
        </button>
        <div class="header-path" v-for="(element, index) in path">
            <button class="btn" @click="pathRevert(index)">
                {{ element }}
            </button>
            <div class="separator" v-if="index < (path.length - 1)">/</div>
        </div>
    </header>
    <div class="container">
        <div class="panel">
            <div class="panel-element" v-for="(folder, index) in folders" @click="goFolder(index)">
                <svg class="icon" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 367.6 367.6" style="enable-background:new 0 0 367.6 367.6;" xml:space="preserve">
                    <g>
                        <path style="fill:#FFA400;" d="M347.928,72.904h-164.61l-1.476-9.574c-1.647-10.693-11.849-19.442-22.669-19.442h-79.18
                            c-10.82,0-21.022,8.749-22.668,19.442l-1.476,9.574h-5.611c-10.819,0-19.672,8.853-19.672,19.672V304.04
                            c0,10.82,8.853,19.672,19.672,19.672h297.689c10.819,0,19.673-8.852,19.673-19.672V92.577
                            C367.602,81.757,358.747,72.904,347.928,72.904z"></path>
                        <rect x="48.797" y="94.106" style="fill:#FFFFFF;" width="295.084" height="200.013"></rect>
                        <path style="fill:#F6C65B;" d="M337.281,135.114c-1.718-10.683-11.976-19.423-22.795-19.423H16.796
                            c-10.82,0-18.268,8.74-16.55,19.423L27.444,304.29c1.718,10.683,11.976,19.423,22.795,19.423h297.689
                            c10.819,0,18.267-8.74,16.55-19.423L337.281,135.114z"></path>
                    </g>
                    </svg>
                {{ folder }}
            </div>
        </div>
        <div class="panel">
            <div class="panel-element" v-for="(file, index) in files">
                <!-- svg JS/JSON -->
                <svg version="1.1" class="icon" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"
                     v-if="['json', 'js'].indexOf(file.type) != -1">
                    <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path>
                    <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path>
                    <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon>
                    <path style="fill:#576D7E;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                        V416z"></path>
                    <g>
                        <path style="fill:#FFFFFF;" d="M193.744,303.152c0-10.752,16.896-10.752,16.896,0v50.528c0,20.08-9.6,32.24-31.712,32.24
                            c-10.88,0-19.968-2.96-27.888-13.168c-6.528-7.808,5.744-19.056,12.4-10.88c5.376,6.656,11.12,8.192,16.752,7.92
                            c7.168-0.256,13.44-3.456,13.568-16.112v-50.528H193.744z"></path>
                        <path style="fill:#FFFFFF;" d="M230.272,314.656c2.944-24.816,40.416-29.28,58.08-15.712c8.704,7.024-0.512,18.16-8.192,12.528
                            c-9.472-6.016-30.96-8.832-33.648,4.464c-3.456,20.992,52.192,8.976,51.296,42.992c-0.896,32.496-47.968,33.264-65.632,18.672
                            c-4.224-3.44-4.096-9.056-1.792-12.528c3.328-3.312,7.024-4.464,11.392-0.896c10.48,7.168,37.488,12.544,39.408-5.648
                            C279.52,339.616,226.304,351.008,230.272,314.656z"></path>
                    </g>
                    <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path>
                    </svg>
                <!-- Fin svg JS/JSON -->
                <!-- svg PHP -->
                <svg version="1.1" class="icon" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"
                     v-if="['php'].indexOf(file.type) != -1">
                    <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path>
                    <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path>
                    <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon>
                    <path style="fill:#576D7E;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                        V416z"></path>
                    <g>
                        <path style="fill:#FFFFFF;" d="M102.912,303.152c0-4.224,3.328-8.848,8.704-8.848h29.552c16.64,0,31.616,11.136,31.616,32.496
                            c0,20.224-14.976,31.472-31.616,31.472h-21.376v16.896c0,5.648-3.568,8.832-8.176,8.832c-4.224,0-8.704-3.184-8.704-8.832
                            C102.912,375.168,102.912,303.152,102.912,303.152z M119.792,310.432v31.856h21.36c8.576,0,15.36-7.552,15.36-15.488
                            c0-8.96-6.784-16.368-15.36-16.368L119.792,310.432L119.792,310.432z"></path>
                        <path style="fill:#FFFFFF;" d="M186.48,376.064v-72.656c0-4.608,3.328-7.936,9.088-7.936c4.464,0,7.792,3.328,7.792,7.936v27.888
                            h41.696v-27.888c0-4.608,4.096-7.936,8.704-7.936c4.464,0,7.936,3.328,7.936,7.936v72.656c0,4.48-3.472,7.936-7.936,7.936
                            c-4.608,0-8.704-3.456-8.704-7.936v-28H203.36v28c0,4.48-3.328,7.936-7.792,7.936C189.808,384,186.48,380.544,186.48,376.064z"></path>
                        <path style="fill:#FFFFFF;" d="M279.664,303.152c0-4.224,3.328-8.848,8.704-8.848h29.552c16.64,0,31.616,11.136,31.616,32.496
                            c0,20.224-14.976,31.472-31.616,31.472h-21.36v16.896c0,5.648-3.584,8.832-8.192,8.832c-4.224,0-8.704-3.184-8.704-8.832V303.152
                            L279.664,303.152z M296.56,310.432v31.856h21.36c8.576,0,15.36-7.552,15.36-15.488c0-8.96-6.784-16.368-15.36-16.368
                            L296.56,310.432L296.56,310.432z"></path>
                    </g>
                    <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path>
                    </svg>
                <!-- Fin svg PHP -->
                <!-- svg SVG -->
                <svg version="1.1" class="icon" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"
                     v-if="['svg'].indexOf(file.type) != -1">
                <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path>
                    <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path>
                    <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon>
                    <path style="fill:#F7B84E;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                    V416z"></path>
                    <g>
                        <path style="fill:#FFFFFF;" d="M96.816,314.656c2.944-24.816,40.416-29.28,58.08-15.712c8.704,7.024-0.512,18.16-8.192,12.528
                        c-9.472-6.016-30.96-8.832-33.648,4.464c-3.456,20.992,52.192,8.976,51.312,42.992c-0.896,32.496-47.984,33.264-65.648,18.672
                        c-4.224-3.44-4.096-9.056-1.792-12.528c3.328-3.312,7.04-4.464,11.392-0.896c10.48,7.168,37.488,12.544,39.392-5.648
                        C146.064,339.616,92.848,351.008,96.816,314.656z"></path>
                        <path style="fill:#FFFFFF;" d="M209.12,378.256l-33.776-70.752c-4.992-10.112,10.112-18.416,15.728-7.808l11.392,25.712
                        l14.704,33.776l14.448-33.776l11.392-25.712c5.12-9.712,19.952-3.584,15.616,7.04L226,378.256
                        C223.056,386.32,213.984,388.224,209.12,378.256z"></path>
                        <path style="fill:#FFFFFF;" d="M345.76,374.16c-9.088,7.536-20.224,10.752-31.472,10.752c-26.88,0-45.936-15.36-45.936-45.808
                        c0-25.84,20.096-45.92,47.072-45.92c10.112,0,21.232,3.456,29.168,11.264c7.792,7.664-3.456,19.056-11.12,12.288
                        c-4.736-4.624-11.392-8.064-18.048-8.064c-15.472,0-30.432,12.4-30.432,30.432c0,18.944,12.528,30.448,29.296,30.448
                        c7.792,0,14.448-2.304,19.184-5.76V348.08h-19.184c-11.392,0-10.24-15.632,0-15.632h25.584c4.736,0,9.072,3.6,9.072,7.568v27.248
                        C348.96,369.552,347.936,371.712,345.76,374.16z"></path>
                    </g>
                    <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path>
                </svg>
                <!-- Fin svg SVG -->
                <!-- svg HTML -->
                <svg version="1.1" class="icon" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"
                     v-if="['html', 'xhtml'].indexOf(file.type) != -1">
                    <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path>
                    <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path>
                    <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon>
                    <path style="fill:#84BD5A;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                    V416z"></path>
                    <g>
                        <path style="fill:#FFFFFF;" d="M55.68,376.064v-72.656c0-4.608,3.328-7.936,9.072-7.936c4.48,0,7.808,3.328,7.808,7.936v27.888
                            h41.696v-27.888c0-4.608,4.096-7.936,8.704-7.936c4.48,0,7.936,3.328,7.936,7.936v72.656c0,4.48-3.456,7.936-7.936,7.936
                            c-4.608,0-8.704-3.456-8.704-7.936v-28H72.56v28c0,4.48-3.328,7.936-7.808,7.936C59.008,384,55.68,380.544,55.68,376.064z"></path>
                        <path style="fill:#FFFFFF;" d="M172.784,311.472H150.4c-11.136,0-11.136-16.368,0-16.368h60.496c11.392,0,11.392,16.368,0,16.368
                            h-21.232v64.592c0,11.12-16.896,11.392-16.896,0v-64.592H172.784z"></path>
                        <path style="fill:#FFFFFF;" d="M248.688,327.84v47.328c0,5.648-4.608,8.832-9.2,8.832c-4.096,0-7.68-3.184-7.68-8.832v-72.016
                            c0-6.656,5.648-8.848,7.68-8.848c3.696,0,5.872,2.192,8.048,4.624l28.16,37.984l29.152-39.408c4.24-5.232,14.592-3.2,14.592,5.648
                            v72.016c0,5.648-3.6,8.832-7.68,8.832c-4.592,0-8.192-3.184-8.192-8.832V327.84l-21.232,26.864c-4.592,5.648-10.352,5.648-14.576,0
                            L248.688,327.84z"></path>
                        <path style="fill:#FFFFFF;" d="M337.264,303.152c0-4.224,3.584-7.808,8.064-7.808c4.096,0,7.552,3.6,7.552,7.808v64.096h34.8
                            c12.528,0,12.8,16.752,0,16.752h-42.336c-4.48,0-8.064-3.184-8.064-7.808v-73.04H337.264z"></path>
                    </g>
                    <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path>
                </svg>
                <!-- Fin svg HTML -->
                <!-- svg CSS -->
                <svg version="1.1" class="icon" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"
                     v-if="['css', 'scss', 'less', 'sass'].indexOf(file.type) != -1">
                    <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path>
                    <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path>
                    <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon>
                    <path style="fill:#576D7E;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                        V416z"></path>
                    <g>
                        <path style="fill:#FFFFFF;" d="M103.936,339.088c0-24.688,15.472-45.92,44.912-45.92c11.12,0,19.952,3.328,29.296,11.392
                            c3.456,3.184,3.824,8.816,0.368,12.4c-3.456,3.056-8.704,2.688-11.76-0.384c-5.248-5.504-10.624-7.024-17.904-7.024
                            c-19.696,0-29.168,13.952-29.168,29.552c0,15.872,9.344,30.448,29.168,30.448c7.28,0,14.064-2.96,19.952-8.192
                            c3.968-3.072,9.472-1.552,11.76,1.536c2.048,2.816,3.072,7.552-1.408,12.016c-8.96,8.336-19.696,10-30.32,10
                            C117.888,384.912,103.936,363.776,103.936,339.088z"></path>
                        <path style="fill:#FFFFFF;" d="M195.712,314.656c2.944-24.816,40.416-29.28,58.08-15.712c8.704,7.024-0.512,18.16-8.192,12.528
                            c-9.472-6.016-30.96-8.832-33.648,4.464c-3.456,20.992,52.192,8.976,51.296,42.992c-0.896,32.496-47.968,33.264-65.632,18.672
                            c-4.24-3.44-4.096-9.056-1.792-12.528c3.328-3.312,7.024-4.464,11.392-0.896c10.48,7.168,37.488,12.544,39.392-5.648
                            C244.976,339.616,191.744,351.008,195.712,314.656z"></path>
                        <path style="fill:#FFFFFF;" d="M276.48,314.656c2.944-24.816,40.416-29.28,58.08-15.712c8.704,7.024-0.512,18.16-8.192,12.528
                            c-9.472-6.016-30.96-8.832-33.648,4.464c-3.456,20.992,52.192,8.976,51.296,42.992c-0.896,32.496-47.968,33.264-65.632,18.672
                            c-4.224-3.44-4.096-9.056-1.792-12.528c3.328-3.312,7.024-4.464,11.392-0.896c10.48,7.168,37.488,12.544,39.408-5.648
                            C325.728,339.616,272.512,351.008,276.48,314.656z"></path>
                    </g>
                    <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path>
                </svg>
                <!-- Fin svg CSS -->
                <!-- svg SQL -->
                <svg version="1.1" class="icon" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve"
                     v-if="['sql'].indexOf(file.type) != -1">
                    <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path>
                    <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path>
                    <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon>
                    <path style="fill:#F15642;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                        V416z"></path>
                    <g>
                        <path style="fill:#FFFFFF;" d="M98.128,314.672c2.944-24.832,40.416-29.296,58.064-15.728c8.704,7.024-0.496,18.16-8.192,12.528
                            c-9.456-6-30.96-8.816-33.648,4.464c-3.456,20.992,52.208,8.976,51.296,43.008c-0.896,32.496-47.968,33.248-65.632,18.672
                            c-4.224-3.456-4.096-9.072-1.776-12.544c3.312-3.312,7.024-4.464,11.376-0.88c10.496,7.152,37.488,12.528,39.408-5.648
                            C147.376,339.632,94.16,351.008,98.128,314.672z"></path>
                        <path style="fill:#FFFFFF;" d="M265.488,369.424l2.048,2.416c8.432,7.68-2.56,20.224-11.136,12.16l-4.336-3.44
                            c-6.656,4.592-14.448,6.784-24.816,6.784c-22.512,0-48.24-15.504-48.24-46.976s25.584-47.456,48.24-47.456
                            c23.776,0,47.072,15.984,47.072,47.456C274.32,352.528,271.232,361.504,265.488,369.424z M257.792,340.368
                            c0-20.336-15.984-30.688-30.56-30.688c-15.728,0-31.216,10.336-31.216,30.688c0,15.504,13.168,30.208,31.216,30.208
                            c4.592,0,9.072-1.152,13.552-2.304l-14.576-13.44c-6.784-8.192,3.968-19.84,12.528-12.288l14.464,14.448
                            C256.384,352.528,257.792,347.024,257.792,340.368z"></path>
                        <path style="fill:#FFFFFF;" d="M293.168,303.152c0-4.224,3.584-7.808,8.064-7.808c4.096,0,7.552,3.6,7.552,7.808v64.096h34.8
                            c12.528,0,12.8,16.752,0,16.752h-42.336c-4.48,0-8.064-3.184-8.064-7.792v-73.056H293.168z"></path>
                    </g>
                    <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path>
                </svg>
                <!-- Fin svg SQL -->
                <!-- svg executable -->
                <svg version="1.1" class="icon" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 52 52" style="enable-background:new 0 0 52 52;" xml:space="preserve"
                     v-if="['bat', 'sh', 'exe'].indexOf(file.type) != -1">
                    <path style="fill:#C7CAC7;" d="M48.872,22.898c-4.643-0.893-6.772-6.318-3.976-10.13l1.964-2.678l-4.243-4.243l-2.638,1.787
                        c-3.914,2.652-9.256,0.321-9.974-4.351L29.5,0h-6l-0.777,4.041c-0.873,4.54-6.105,6.708-9.933,4.115L9.383,5.847L5.14,10.09
                        l1.964,2.678c2.796,3.812,0.666,9.237-3.976,10.13L0,23.5v6l3.283,0.505c4.673,0.719,7.003,6.061,4.351,9.975l-1.787,2.637
                        l4.243,4.243l2.678-1.964c3.812-2.796,9.237-0.667,10.13,3.976L23.5,52h6l0.355-2.309c0.735-4.776,6.274-7.071,10.171-4.213
                        l1.884,1.382l4.243-4.243l-1.787-2.637c-2.651-3.914-0.321-9.256,4.351-9.975L52,29.5v-6L48.872,22.898z M26.5,31
                        c-2.485,0-4.5-2.015-4.5-4.5s2.015-4.5,4.5-4.5s4.5,2.015,4.5,4.5S28.985,31,26.5,31z"></path>
                    <path style="fill:#556080;" d="M26.5,17c-5.247,0-9.5,4.253-9.5,9.5s4.253,9.5,9.5,9.5s9.5-4.253,9.5-9.5S31.747,17,26.5,17z
                        M26.5,31c-2.485,0-4.5-2.015-4.5-4.5s2.015-4.5,4.5-4.5s4.5,2.015,4.5,4.5S28.985,31,26.5,31z"></path>
                </svg>
                <!-- Fin svg executable -->
                <!-- Tous les autres -->
                <svg v-if="['json', 'js', 'php', 'svg', 'html', 'xhtml', 'css', 'scss', 'less', 'sass', 'sql', 'txt', 'png', 'zip', 'jpg', 'jpeg', 'bat', 'sh', 'exe'].indexOf(file.type) === -1"
                     class="icon" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                     viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
                    <path style="fill:#E2E5E7;" d="M128,0c-17.6,0-32,14.4-32,32v448c0,17.6,14.4,32,32,32h320c17.6,0,32-14.4,32-32V128L352,0H128z"></path>
                    <path style="fill:#B0B7BD;" d="M384,128h96L352,0v96C352,113.6,366.4,128,384,128z"></path>
                    <polygon style="fill:#CAD1D8;" points="480,224 384,128 480,128 "></polygon>
                    <path style="fill:#576D7E;" d="M416,416c0,8.8-7.2,16-16,16H48c-8.8,0-16-7.2-16-16V256c0-8.8,7.2-16,16-16h352c8.8,0,16,7.2,16,16
                        V416z"></path>
                    <g>
                        <path style="fill:#FFFFFF;" d="M94.912,375.68c0,11.12-17.024,11.504-17.024,0.256V303.28c0-4.48,3.472-7.808,7.68-7.808H119.6
                            c32.48,0,39.136,43.504,12.016,54.368l17.008,20.72c6.656,9.856-6.64,19.312-14.336,9.6l-19.312-27.632H94.912V375.68z
                            M94.912,337.808H119.6c16.624,0,17.664-26.864,0-26.864H94.912V337.808z"></path>
                        <path style="fill:#FFFFFF;" d="M162.624,384c-4.096-2.32-6.656-6.912-4.096-12.288l36.704-71.76c3.456-6.784,12.672-7.04,15.872,0
                            l36.064,71.76c5.248,9.968-10.24,17.904-14.832,7.936l-5.648-11.264h-47.2l-5.504,11.264C171.952,384,167.216,384.912,162.624,384z
                            M217.632,351.504l-14.448-31.6l-15.728,31.6H217.632z"></path>
                        <path style="fill:#FFFFFF;" d="M341.248,353.424l19.056-52.704c3.84-10.352,19.312-5.504,15.488,5.632l-25.328,68.704
                            c-2.32,7.296-4.48,9.472-8.832,9.472c-4.608,0-6.016-2.832-8.576-7.424L310.8,326.576l-21.248,49.76
                            c-2.304,5.36-4.464,8.432-9.072,8.432c-4.464,0-6.784-3.072-8.832-8.704l-24.816-69.712c-3.84-11.504,12.4-15.728,15.728-5.632
                            l18.944,52.704l22.64-52.704c3.056-7.808,11.12-8.192,14.448-0.368L341.248,353.424z"></path>
                    </g>
                    <path style="fill:#CAD1D8;" d="M400,432H96v16h304c8.8,0,16-7.2,16-16v-16C416,424.8,408.8,432,400,432z"></path>
                </svg>
                <!-- Fin du svg other -->
                {{ file.name }}
            </div>
        </div>
    </div>
    <footer class="footer">
        Explorateur de fichier développé par Quentin Ysambert (quenti77)
    </footer>
</div>

<script>
    const ajax = (type, url, data) => {
        return new Promise((resolve, reject) => {
            let req = new XMLHttpRequest()

            let params = []
            for (let index in data) {
                if (data.hasOwnProperty(index)) {
                    params.push(index + '=' + data[index])
                }
            }

            let paramsUrl = encodeURI(params.join('&'))
            req.open(type, url, true)
            req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')

            req.onreadystatechange = () => {
                if (req.readyState === 4) {
                    if (req.status === 200) {
                        resolve(req.responseText)
                    } else {
                        reject(req.status, req.responseText)
                    }
                }
            }

            req.send(paramsUrl)
        })
    }

    const vm = new Vue({
        el: '#app',
        data: {
            path: ['~'],
            folders: [],
            files: []
        },
        methods: {
            pathRevert (index) {
                if (index < 0 || index > this.path.length) {
                    return false
                }

                this.path = this.path.slice(0, index + 1)
                this.request()
            },
            parentFolder () {
                if (this.path.length > 1) {
                    this.pathRevert(this.path.length - 2)
                }
            },
            refresh () {
                this.request()
            },
            goFolder (index) {
                const newFolder = this.folders[index]

                if (newFolder === '..') {
                    this.parentFolder()
                } else {
                    this.path.push(newFolder)
                    this.request()
                }
            },
            request () {
                const folder = this.path.join('/')

                ajax('POST', '/index.php', {
                    folder: folder
                }).then((response) => {
                    try {
                        const json = JSON.parse(response)

                        this.folders = json.folders
                        this.files = json.files
                    } catch (e) {
                        // Nothing
                    }
                }).catch((status, response) => {
                    // TODO: ERROR
                })
            }
        },
        mounted () {
            this.request()
        }
    })
</script>

</body>
</html>