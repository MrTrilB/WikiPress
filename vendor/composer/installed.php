<?php return array(
    'root' => array(
        'name' => 'trilbdev/wikipress',
        'pretty_version' => 'dev-main',
        'version' => 'dev-main',
        'reference' => 'e6292db3d8d1cd0b88e5021ee93701dbd1edbd17',
        'type' => 'wordpress-plugin',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        'trilbdev/bootstrapphp' => array(
            'pretty_version' => 'dev-Master',
            'version' => 'dev-Master',
            'reference' => '4f81c7493906d0cc5ef6d5787596fb195e075619',
            'type' => 'library',
            'install_path' => __DIR__ . '/../trilbdev/bootstrapphp',
            'aliases' => array(
                0 => '1.0.x-dev',
            ),
            'dev_requirement' => false,
        ),
        'trilbdev/wikipress' => array(
            'pretty_version' => 'dev-main',
            'version' => 'dev-main',
            'reference' => 'e6292db3d8d1cd0b88e5021ee93701dbd1edbd17',
            'type' => 'wordpress-plugin',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'twbs/bootstrap' => array(
            'pretty_version' => 'v5.3.8',
            'version' => '5.3.8.0',
            'reference' => '25aa8cc0b32f0d1a54be575347e6d84b70b1acd7',
            'type' => 'library',
            'install_path' => __DIR__ . '/../twbs/bootstrap',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'twitter/bootstrap' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => 'v5.3.8',
            ),
        ),
    ),
);
