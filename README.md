# php-doh-proxy
DNS over HTTPS proxy written in PHP. Simple but works.

## What is this for?
This tiny PHP script forwards DoH request to another DoH server. This can be useful for some network environment where most public DoH servers are blocked.

## How to use it?
Get a domain and some PHP environment (VPS, web hosting etc.), upload `doh-proxy.php` (rename as you want for example `dns-query.php`, you can set some rewrite if you want to use `https://your-domain/dns-query`), and set the url in your browser.


