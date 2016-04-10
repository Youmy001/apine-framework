<IfModule mod_deflate.c>
	<IfModule mod_setenvif.c>
		<IfModule mod_headers.c>
			Header unset Pragma
			FileETag None
			Header unset ETag
			SetEnvIfNoCase ^(Accept-EncodXng|X-cept-Encoding|X{15}|~{15}|-{15})$ ^((gzip|deflate)\s*,?\s*)+|[X~-]{4,13}$ HAVE_Accept-Encoding
			RequestHeader append Accept-Encoding "gzip,deflate" env=HAVE_Accept-Encoding
		</IfModule>
	</IfModule>

	# HTML, TXT, CSS, JavaScript, JSON, XML, HTC:
	<IfModule filter_module.c>
		FilterDeclare   COMPRESS
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/html
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/css
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/plain
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/xml
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $text/x-component
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/javascript
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/json
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/xml
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/xhtml+xml
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/rss+xml
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/atom+xml
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/vnd.ms-fontobject
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $image/svg+xml
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $application/x-font-ttf
		FilterProvider  COMPRESS  DEFLATE resp=Content-Type $font/opentype
		FilterChain     COMPRESS
		FilterProtocol  COMPRESS  DEFLATE change=yes;byteranges=no
	</IfModule>
	
	<IfModule mod_filter.c>
		# Legacy versions of Apache
		AddOutputFilterByType DEFLATE text/html text/plain text/css application/json
		AddOutputFilterByType DEFLATE application/javascript
		AddOutputFilterByType DEFLATE text/xml application/xml text/x-component
		AddOutputFilterByType DEFLATE application/xhtml+xml application/rss+xml application/atom+xml
		AddOutputFilterByType DEFLATE image/svg+xml application/vnd.ms-fontobject application/x-font-ttf font/opentype
	</IfModule>

</IfModule>

<ifModule mod_headers.c>
    # Turn on Expires and set default expires to 7 days
    ExpiresActive On
    ExpiresDefault A604800

    # Set up caching on media files and fonts for 1 month
    <filesMatch ".(ico|gif|jpg|jpeg|png|swf|mp3|mp4|ttf|woff)$">
        ExpiresDefault A2419200
        Header append Cache-Control "public"
    </filesMatch>

    # Set up 1 Week caching on commonly updated files
    <filesMatch ".(html|js|css)$">
        #ExpiresDefault A2678400
        ExpiresDefault A0
        #Header append Cache-Control "private, must-revalidate"
        Header append Cache-Control "no-store, no-cache, must-revalidate, max-age=0"
    </filesMatch>

    # Force no caching for dynamic files
    <filesMatch ".(php|cgi)$">
        ExpiresDefault A0
        Header set Cache-Control "no-store, no-cache, must-revalidate, max-age=0"
        Header set Pragma "no-cache"
    </filesMatch>
</ifModule>

#Options +FollowSymlinks
#Options +SymLinksIfOwnerMatch
<IfModule mod_rewrite.c>
	RewriteEngine On
	
	RewriteCond %{REQUEST_FILENAME} -f
	RewriteRule ^(<?= $parent_name ?>/|controllers/|modules/|views/|config\.ini|config_sample\.ini|\.xml|composer\.*|VERSION) - [F,L,NC]

	# APIne Rules
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^<?= $webroot ?>api/((.+))?$ /?request=/$1&api=api [QSA,L]
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^<?= $webroot ?>([a-zA-Z]{2}(-[a-zA-Z]{2})?)(/(.+)?)?$ /?request=$3&language=$1 [QSA,L]
	
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^<?= $webroot ?>(.*)$ /?request=/$1 [QSA,L]

</IfModule>