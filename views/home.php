<?php
// Render dynamic SPA homepage by embedding the existing UI HTML moved previously.
// For now, redirect to legacy UI route handled by SPA markup was removed; implement a minimal container or reuse previous HTML if needed.
// This placeholder can be upgraded to server-side rendered catalog later.
require App\Core\App::$basePath . '/legacy_home.html.php';
