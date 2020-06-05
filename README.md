# docker-nginx-php-fpm
Docker container with Nginx and PHP-FPM in one

## Config

Any files added to the `sites-enabled` folder will be loaded so in addition to the `default.conf` you may want to redirect multiple domains to a new owner

Example:

`/etc/nginx/sites-enabled/old-domains.conf`

```
server {
    listen 80;
    listen 443 ssl;
    server_name www.old-name.com old-name.com;
    return 301 $scheme://www.new-name.com$request_uri;
}
```


Any `*.conf` files added to the `extras-enabled` folder will be added inside the primary `server{}` directive.

Examples:

`/etc/nginx/extras-enabled/rewrites.conf`

```
rewrite ^(/download/.*)/media/(\w+)\.?.*$ $1/mp3/$2.mp3 last;
```

`/etc/nginx/extras-enabled/security.conf`

```
location ~ .(aspx|php|jsp|cgi)$ {
    deny all;
}
```

## Wordpress

Trailing slash URLs enabled by default if `WP_ENV` is present

### Bedrock

Enable rewrite rules with ENV `WP_FRAMEWORK=bedrock`

### Multisite

Add the following rules to `/etc/nginx/extras-enabled/rewrites.conf`

```
rewrite ^(/[^/]+)?(/wp-.*) $2 last;
rewrite ^(/[^/]+)?(/.*\.php) $2 last;
```

## Troubleshooting

### Increase nginx pm.max_childen

**/etc/php/7.0/fpm/pool.d/www.conf**

```
[php-fpm-pool-settings]
pm = dynamic
pm.max_children = 25
pm.start_servers = 10
pm.min_spare_servers = 5
pm.max_spare_servers = 20
pm.max_requests = 500
```

To get an idea of what to use for the `pm.max_children`, you can use this calculation: _pm.max_children_ = _Total RAM dedicated to the web server_ / _Max child process size_.

 Remember to leave some RAM for extra services you have running on your system.

Determine the memory used by each (PHP-FPM) child process

```
procps -ylC php-fpm7.2 --sort:rss
```

Check an average memory usage by single PHP-FPM process

```
procps --no-headers -o "rss,cmd" -C php-fpm | awk '{ sum+=$1 } END { printf ("%d%s\n", sum/NR/1024,"M") }'
```
