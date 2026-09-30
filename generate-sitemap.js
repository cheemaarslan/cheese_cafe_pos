const { SitemapStream } = require('sitemap');
const { createWriteStream } = require('fs');

const links = [
  { url: '/', changefreq: 'daily', priority: 1.0 },
  { url: '/home', changefreq: 'daily', priority: 1.0 },
  { url: '/menu', changefreq: 'daily', priority: 0.9 },
  { url: '/offers', changefreq: 'daily', priority: 0.8 },
  { url: '/our-specials', changefreq: 'weekly', priority: 0.8 },
  { url: '/blog', changefreq: 'weekly', priority: 0.7 },
  { url: '/contact', changefreq: 'monthly', priority: 0.6 },
  { url: '/about-us', changefreq: 'monthly', priority: 0.5 },
];

const sitemap = new SitemapStream({ hostname: 'https://3guysburgersandgyros.com' });
const writeStream = createWriteStream('./public/sitemap.xml');

sitemap.pipe(writeStream);
links.forEach((link) => sitemap.write(link));
sitemap.end();