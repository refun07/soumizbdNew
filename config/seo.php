<?php
return [
    // Enable only for the final public deployment. Local/staging copies stay out of search.
    'indexable' => env('SEO_INDEXABLE', env('APP_ENV') === 'production'),
];
