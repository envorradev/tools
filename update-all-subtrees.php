<?php declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Envorra\GitHelper\Routines\UpdateAllSubtreesRoutine;

$routine = new UpdateAllSubtreesRoutine(
    repositoryBaseUrl: 'git@github.com:envorradev/',
    prefixMap: [
        'tool-arrays' => 'src/Arrays',
        'tool-common' => 'src/Common',
        'tool-composer' => 'src/Composer',
        'tool-filesystem' => 'src/Filesystem',
        'tool-flags' => 'src/Flags',
        'tool-json' => 'src/Json',
    ]
);

$routine->pushTags()->run();
