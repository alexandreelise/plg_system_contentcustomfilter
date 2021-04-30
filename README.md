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

## COMMUNITY

> In English:

Get in touch on social media or contact me directly

* Website: [https://coderparlerpartager.fr/en](https://coderparlerpartager.fr/en)
* Contact: [Contact me](https://coderparlerpartager.fr/en/say-hello)
* Newsletter: [Weekly newsletter of technical blog](https://coderparlerpartager.fr/en/get-newsletter)

---

> En français

Contactez-moi directement ou bien sur les réseaux sociaux

* Site web: [https://coderparlerpartager.fr](https://coderparlerpartager.fr)
* Contact: [Me contacter](https://coderparlerpartager.fr/contact)
* Newsletter: [Newsletter hebdomadaire blog technique](https://coderparlerpartager.fr/newsletter)

---

* Twitter: [@mralexandrelise](https://twitter.com/mralexandrelise)
* Facebook: [coderparlerpartager](https://www.facebook.com/coderparlerpartager)
* Linkedin: [coderparlerpartager](https://www.linkedin.com/company/coderparlerpartager)
* Youtube: [coderparlerpartager](https://www.youtube.com/channel/UCCya8rIL-PVHm8Mt4QPW-xw?sub_confirmation=1)
