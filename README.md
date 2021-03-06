# System - Content Custom Filter

System - Content Custom Filter. Custom fields filter plugin using Joomla! core features.

## INSTALLATION

* Go to build folder of this repo
* Download the latest extension file in this build folder
* Install the extension on your Joomla 4 Beta website (development site)

## USAGE

1. Create article type custom fields with descriptive names like "article-type-of-dish", or "article-quantity" or "
   article-start-date"
2. Go to your joomla website with a blog category menu item them in your address bar, if there aren't any query string
   type for example:

```

https://example.com/blog/?filterfield[article-type-of-dish]=Dessert

```   

The revelant part is ?filterfield[article-type-of-dish]=Dessert

Otherwise if there is already a query string type

```

https://example.com/blog/?alreadythere=abc&filterfield[article-type-of-dish]=Dessert

```

The revelant part is &filterfield[article-type-of-dish]=Dessert

If everything went ok, you should only see the articles which match the custom field criteria you chose.

3. You can eventually invert the match by adding after what you just typed

```

&is_included=0

```

or

```

&is_included=1

```
--------------------------------------------
## INFOS

> English: [Click here to get in touch](https://github.com/mralexandrelise/mralexandrelise/blob/master/community.md "Get in touch")

> Français: [Cliquez ici pour me contacter](https://github.com/mralexandrelise/mralexandrelise/blob/master/community.md "Me contacter")
