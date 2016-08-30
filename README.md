# Purifier

Purifier is a very simple and powerful PHP class that allows to purify an HTML or BBcode input, authorize or delete the URLs you want, convert HTML to BBCode, and enable or disable JS scripts.

***
## Class Purify for HTML
### Get started :
By default, the following tags are allowed :
```
<p><br /><br/><br><a><b><strong><em><i><ul><ol><li><blockquote>
```
(*$basic_tags* in purifier.php line 4 to change it).

If the program find a non-authorized tag in the input string, it will simply delete it (only the non-authorized HTML tags).

#### Initialize a basic class :
```
<?php
$purifier = new Purify($authorized_tags="BASIC",$other_tags="",$verbose=false);
$output = $purifier->purify($input);
?>
```

* *$authorized_tags* allows to define a class with pre-defined HTML tags. Default is "BASIC", can be "NONE" for no default tag.
* *$other_tags* [facultative] allows to define any other HTML tag you want to be authorized.
* *$verbose* [facultative] allows to debug the class to what's going on.
* *purify($input)* this function satanizes the $input checking the forbidden or broken tags.

##### Example 1 : you just want to authorize the ```<b>``` and ```<p>``` tag.
```
$purifier = new Purify("NONE","<b><p>");
```
If the input is ```<p><b>Blabla</b><i>Another blabla</i></p>```, the ouput will be : ```<p><b>Blabla</b>Another blabla</p>```
##### Example 2 : you want to authorize the ```<b>``` tag AND all the basic tags.
```
$purifier = new Purify("BASIC","<b>");
```

#### Initialize a class with controlled URLs
This feature allows you to control which domain name is authorized or not. If a non-authorized domain name is found, the link be deleted.
```
$purifier = new Purify("BASIC");
$purifier->setAuthorizedUrl($authorized_url);
$output = $purifier->purify($input);
```

* *$authorized_url* : the domain names you authorize, **separated by a dot** (,).
* *$input* : the string you want to check.
* *purify()* automatically uses the *VerifUrl()* function that checks the authorized links if not _null_

##### Example 1 :

_INPUT :_
```
$purifier = new Purify("BASIC");
$purifier->setAuthorizedUrl("science.com,wikipedia.com");
$purifier->purify("<a href='http://example.com/blabla'>A simple example</a>");
```

_OUTPUT :_
```
<a href=''>A simple example</a>
```
As you see, as the domain *example.com* has not been declared in *setAuthorizedUrl()*, it has been removed.

#### Authorize JavaScript scripts :
```
$purifier = new Purify("BASIC");
$purifier->setAuthorizeScripts($boolean); // TRUE OR FALSE
```
