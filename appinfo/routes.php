<?php

declare(strict_types=1);

return [
    'routes' => [
        // Page routes
        ['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
        
        // Account management routes
        ['name' => 'account#list', 'url' => '/api/accounts', 'verb' => 'GET'],
        ['name' => 'account#create', 'url' => '/api/accounts', 'verb' => 'POST'],
        ['name' => 'account#get', 'url' => '/api/accounts/{id}', 'verb' => 'GET'],
        ['name' => 'account#update', 'url' => '/api/accounts/{id}', 'verb' => 'PUT'],
        ['name' => 'account#delete', 'url' => '/api/accounts/{id}', 'verb' => 'DELETE'],
        ['name' => 'account#test', 'url' => '/api/accounts/{id}/test', 'verb' => 'POST'],
        
        // Repository routes
        ['name' => 'repository#list', 'url' => '/api/repositories', 'verb' => 'GET'],
        ['name' => 'repository#fetch', 'url' => '/api/repositories/fetch', 'verb' => 'POST'],
        ['name' => 'repository#get', 'url' => '/api/repositories/{id}', 'verb' => 'GET'],
        ['name' => 'repository#link', 'url' => '/api/repositories/link', 'verb' => 'POST'],
        ['name' => 'repository#unlink', 'url' => '/api/repositories/{id}/unlink', 'verb' => 'POST'],
        ['name' => 'repository#details', 'url' => '/api/repositories/{id}/details', 'verb' => 'GET'],
        
        // Branch routes
        ['name' => 'branch#list', 'url' => '/api/repositories/{id}/branches', 'verb' => 'GET'],
        ['name' => 'branch#switch', 'url' => '/api/repositories/{id}/branches/switch', 'verb' => 'POST'],
        
        // Sync routes
        ['name' => 'sync#sync', 'url' => '/api/repositories/{id}/sync', 'verb' => 'POST'],
        ['name' => 'sync#status', 'url' => '/api/repositories/{id}/sync/status', 'verb' => 'GET'],
        ['name' => 'sync#history', 'url' => '/api/repositories/{id}/sync/history', 'verb' => 'GET'],
        ['name' => 'sync#conflicts', 'url' => '/api/repositories/{id}/sync/conflicts', 'verb' => 'GET'],
        ['name' => 'sync#resolveConflict', 'url' => '/api/sync/conflicts/{conflictId}/resolve', 'verb' => 'POST'],
        
        // Documentation routes
        ['name' => 'documentation#list', 'url' => '/api/repositories/{id}/docs', 'verb' => 'GET'],
        ['name' => 'documentation#get', 'url' => '/api/repositories/{id}/docs/file', 'verb' => 'GET'],
        ['name' => 'documentation#render', 'url' => '/api/repositories/{id}/docs/render', 'verb' => 'POST'],
        
        // Suggestion routes
        ['name' => 'suggestion#create', 'url' => '/api/suggestions', 'verb' => 'POST'],
        ['name' => 'suggestion#list', 'url' => '/api/suggestions', 'verb' => 'GET'],
        ['name' => 'suggestion#get', 'url' => '/api/suggestions/{id}', 'verb' => 'GET'],
        ['name' => 'suggestion#update', 'url' => '/api/suggestions/{id}', 'verb' => 'PUT'],
        ['name' => 'suggestion#approve', 'url' => '/api/suggestions/{id}/approve', 'verb' => 'POST'],
        ['name' => 'suggestion#reject', 'url' => '/api/suggestions/{id}/reject', 'verb' => 'POST'],
        ['name' => 'suggestion#commit', 'url' => '/api/suggestions/{id}/commit', 'verb' => 'POST'],
        
        // Commit routes
        ['name' => 'commit#list', 'url' => '/api/repositories/{id}/commits', 'verb' => 'GET'],
        ['name' => 'commit#get', 'url' => '/api/repositories/{id}/commits/{sha}', 'verb' => 'GET'],
        
        // Release routes
        ['name' => 'release#list', 'url' => '/api/repositories/{id}/releases', 'verb' => 'GET'],
        ['name' => 'release#tags', 'url' => '/api/repositories/{id}/tags', 'verb' => 'GET'],
        ['name' => 'release#generateNotes', 'url' => '/api/repositories/{id}/release-notes/generate', 'verb' => 'POST'],
        ['name' => 'release#saveNotes', 'url' => '/api/repositories/{id}/release-notes/save', 'verb' => 'POST'],
        
        // Activity routes
        ['name' => 'activity#list', 'url' => '/api/activity', 'verb' => 'GET'],
        ['name' => 'activity#repository', 'url' => '/api/activity/repository/{id}', 'verb' => 'GET'],
        
        // Settings routes
        ['name' => 'settings#get', 'url' => '/api/settings', 'verb' => 'GET'],
        ['name' => 'settings#update', 'url' => '/api/settings', 'verb' => 'PUT'],
    ]
];
