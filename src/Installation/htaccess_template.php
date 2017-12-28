#Options +FollowSymlinks
#Options +SymLinksIfOwnerMatch
Options -Indexes
<IfModule mod_rewrite.c>
	RewriteEngine On
	
	RewriteCond %{REQUEST_FILENAME} -f
	RewriteRule ^(<?= $htaccess_parent ?>/|controllers/|modules/|views/|config\.ini|config_sample\.ini|\.xml|composer\.*|VERSION) - [F,L,NC]

	# APIne Rules
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^api/((.+))?$ /?request=/$1&api=api [QSA,L]
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^([a-zA-Z]{2}(-[a-zA-Z]{2})?)(/(.+)?)?$ /?request=$3&language=$1 [QSA,L]
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.*)$ /?request=/$1 [QSA,L]

</IfModule>