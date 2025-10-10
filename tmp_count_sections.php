<?php
$files=[
 'resources/views/admin/visits/index.blade.php',
 'resources/views/admin/visits/create.blade.php',
 'resources/views/admin/visits/edit.blade.php',
 'resources/views/admin/visits/show.blade.php',
 'resources/views/admin/visits/map.blade.php',
 'resources/views/admin/visits/reports.blade.php'
];
foreach($files as $f){
  $c=file_get_contents($f);
  preg_match_all('/@section/', $c,$m1);
  preg_match_all('/@endsection/', $c,$m2);
  echo "$f -> @section: " . count($m1[0]) . ", @endsection: " . count($m2[0]) . PHP_EOL;
}
