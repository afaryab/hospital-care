<?php

return [

    /*
    |-----------------------------------------------------------------
    | Default Prefix
    |-----------------------------------------------------------------
    |
    | This config option allows you to define a default prefix for
    | your icons. The dash separator will be applied automatically
    | to every icon name. It's required and needs to be unique.
    |
    */

    'prefix' => 'healthicons',

    /*
    |-----------------------------------------------------------------
    | Fallback Icon
    |-----------------------------------------------------------------
    |
    | This config option allows you to define a fallback
    | icon when an icon in this set cannot be found.
    |
    */

    'fallback' => '',

    /*
    |-----------------------------------------------------------------
    | Default Set Classes
    |-----------------------------------------------------------------
    |
    | This config option allows you to define some classes which
    | will be applied by default to all icons within this set.
    |
    */

    'class' => 'w-6 h-6', // Tailwind size example

    /*
    |-----------------------------------------------------------------
    | Custom Path (Override)
    |-----------------------------------------------------------------
    |
    | If you want to use a custom icons directory, set the path here.
    | This will override the default package SVG path.
    */
    'path' => resource_path('icons/health-icons'),

    /*
    |-----------------------------------------------------------------
    | Default Set Attributes
    |-----------------------------------------------------------------
    |
    | This config option allows you to define some attributes which
    | will be applied by default to all icons within this set.
    |
    */

    'attributes' => [
        // 'width' => 50,
        // 'height' => 50,
    ],

];
