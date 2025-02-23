# System - Content Custom Filter

> System - Content Custom Filter. Custom fields filter plugin using Joomla! core features.

![visitor badge](https://visitor-badge.laobi.icu/badge?page_id=alexandreelise.plg_system_contentcustomfilter&style=flat&format=true)
![GitHub followers](https://img.shields.io/github/followers/alexandreelise?style=flat)
![YouTube Channel Views](https://img.shields.io/youtube/channel/views/UCCya8rIL-PVHm8Mt4QPW-xw?style=flat&label=YouTube%20%40Api%20Adept%20vues)


<pre>

    __  __     ____         _____                              __                      __              
   / / / ___  / / ____     / ___/__  ______  ___  _____       / ____  ____  ____ ___  / ___  __________
  / /_/ / _ \/ / / __ \    \__ \/ / / / __ \/ _ \/ ___/  __  / / __ \/ __ \/ __ `__ \/ / _ \/ ___/ ___/
 / __  /  __/ / / /_/ /   ___/ / /_/ / /_/ /  __/ /     / /_/ / /_/ / /_/ / / / / / / /  __/ /  (__  ) 
/_/ /_/\___/_/_/\____/   /____/\__,_/ .___/\___/_/      \____/\____/\____/_/ /_/ /_/_/\___/_/  /____/  
                                   /_/                                                                 


</pre>

> ![GitHub Repo stars](https://img.shields.io/github/stars/alexandreelise/plg_system_contentcustomfilter?style=flat) ![GitHub forks](https://img.shields.io/github/forks/alexandreelise/plg_system_contentcustomfilter?style=flat) ![GitHub watchers](https://img.shields.io/github/watchers/alexandreelise/plg_system_contentcustomfilter?style=flat)

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
