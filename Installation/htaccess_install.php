#Options +FollowSymlinks
#Options +SymLinksIfOwnerMatch
Options -Indexes
<IfModule mod_rewrite.c>
	RewriteEngine On
	
	# APIne Rules
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.*)$ <?= $parent_name ?>/install.php?request=/$1 [QSA,L]

</IfModule>