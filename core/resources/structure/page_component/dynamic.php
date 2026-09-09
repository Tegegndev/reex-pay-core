<?php

return [

    'component_fields' => [

        'content' => [
            'translatable' => true,
            'type'         => 'text_editor',
            'class'        => 'col-md-12',
            'validation'   => 'required|string|max:20000',
        ],
    ],

];
