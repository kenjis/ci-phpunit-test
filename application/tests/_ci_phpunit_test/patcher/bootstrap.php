<?php

require __DIR__ . '/CIPHPUnitTestIncludeStream.php';
require __DIR__ . '/CIPHPUnitTestPatchPathChecker.php';
require __DIR__ . '/CIPHPUnitTestPatcher.php';
require __DIR__ . '/MonkeyPatch.php';

// Register include stream wrapper for monkey patching
CIPHPUnitTestPatcher::wrap();

// And you have to set three paths
//CIPHPUnitTestPatcher::setIncludePaths();
//CIPHPUnitTestPatcher::setExcludePaths();
//CIPHPUnitTestPatcher::setCacheDir();
