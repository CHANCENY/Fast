# Fast-framework
>Fast-framework is lightweight php core framework that
>abstract developer from routing process, authenticating users,
>and many more. This framework is _open source framework._
---
## How to use Fast-framework.

>Note these instructions carefully to enjoy using __Fast-framework__
>The following are the requirements
> * All views created will be located in Views directory
> * Mandatory views to have in Views folder
>   * nav.view.php
>   * footer.view.php
> * You must have a view having the following property in Views folder. This will be used as home page
>   * url: index
> * To create view always use build-in form
> * You can make your own login page but signing in and out of users please use build-in Class method for efficient purposes.
---
## Mandatory Edits To Make Before Starting Use This Framework
>On root of this framework package there index.php, setting.php and .htaccess files
> ### .htaccess file
> In this file you have to change this line
> **RewriteBase** */here give your project folder name*
> 
> ### settings.php file
> ```php
>    global $SITEMAP;
>    $SITEMAP = "give sitemap complete url (https://mysite.com/sitemap.xml";
> 
>   global $HOME;
>   $HOME = '/change here give same folder name as in .htaccess';
> 
>   global $HOMEPAGE;
>   $HOMEPAGE = 'change here give home url';
> ```
---
## More Documentation
Visit this website
[Fast Framework](https://fast.com "Fast Framework")