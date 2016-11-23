#Sitemap Generator

This package is a sitemap generator which generates XML sitemap for any given website dynamically. It generates sitemap by scraping all links from webpages recursively. But I tried to make it efficient by reducing the number of loops in least possible value.


#Installation

You can install the package using composer.

```sh
composer require ashfaq1701/sitemap-generator
```

#Usage

After installation using composer require the composer autoload file.

```sh
require 'vendor/autoload.php';
```

After this follow the steps below,

```sh
use Ashfaq1701\SitemapGenerator\SitemapGenerator;

....

$sitemapGenerator = new SitemapGenerator($url, $path, $maximum);
$sitemap->generateSitemap();
```

It will generate the sitemap xml file in the file passed in $path variable. The maximum number of url's in the sitemap will be the number passed in $maximum.

A typical call will be like,

```sh
$sitemapGenerator = new SitemapGenerator('https://www.venturepact.com', '/home/user/sitemap.xml', 2000);
$sitemap->generateSitemap();
```

The $path and $maximum parameters are not mandatory because they assume default values as $path='sitemap.xml' and $maximum=1000.

The generateSitemap() method not only writes the xml sitemap in the given directory but it also returns the sitemap XML as String. You can use this as following,

```sh
$sitemapGenerator = new SitemapGenerator('https://www.venturepact.com', '/home/user/sitemap.xml', 2000);
$sitemapText = $sitemap->generateSitemap();
echo $sitemapText;
```

#Improvements

If you have suggestions or need improvements please create issues or fork. Any improvements are more than welcome.

