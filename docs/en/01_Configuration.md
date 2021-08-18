# Configuration

<!-- TOC depthFrom:2 depthTo:6 withLinks:1 updateOnSave:1 orderedList:0 -->

- [Using DataObjects as pages](#using-dataobjects-as-pages)
	- [Extending the DataObject](#extending-the-dataobject)
	- [Adding an interface](#adding-an-interface)
- [Default Information](#default-information)
- [Render Types](#render-types)

<!-- /TOC -->

As this module is intended to be a drop-in extension, it will work out of the
box for simple page setups and add the `Sharing` tab to the CMS edit view,
allowing content editors to modify how a page is displayed when shared.

More advanced stuff is possible through configuration, which is explained here.

## Using DataObjects as pages
When using DataObjects as pages, the information used when a link is shared must
originate from said object. In order to correctly set up the information source,
you can follow two ways:

### Extending the DataObject
This is the easiest way to add the ability to manage shared information. It will
add the same set of fields as for pages, so editors will feel right at home and
the entire fallback logic can be used. To achieve this, extend your DataObject
with the `Syntro\SilverShare\Extension\ShareExtension`. **Note**: if you are
using fluent to translate your page, you have to add the extension *before*
fluent!
```yaml
---
Name: blogsharing
Before:
  - 'tractorcow/silverstripe-fluent'
---
Article:
  sharing_fallback_image: Image
  sharing_fallback_title: Title
  sharing_fallback_description: Teaser
  sharing_allow_user_overwrite: true
  sharing_available_og_types:
    - article
  sharing_available_twitter_types:
    - summary
    - summary_large_image
  extensions:
    - Syntro\SilverShare\Extension\ShareExtension
```
> Make sure to add the `sharing_allow_user_overwrite` config value to actually
> render the fields to overwrite the sharing information. If you omit this value
> or set it to `false`, the user will not be able to control the information.
> This may however also be done by choice, if you just want to add sharing meta
> to an object and the fallback fields are sufficient.

You also have to add an `AbsoluteLink` method to your DataObject, as this will
be used to render the `og:url` tag.

Now, you have to tell the controller to actually use the DataObject as a source:

```php
class BlogController extends PageController
{
    // ...
    //
    public function read(HTTPRequest $request)
    {
        $article = Article::get()->byID($request->param('ID'));
        $this->setSharingSource($article); // <- This is all you need
        return [
            'Article' => $article
        ]
    }
}
```
If you now navigate to your article, it should contain the article-related metatags
in the `head` block.

### Adding an interface
The second, more fine-grained option, is to add the [`SharingMetaSource`](/src/Interfaces/SharingMetaSource.php)
interface. This option allows you to take full control over how an item returns
its information, but requires you to implement each value by yourself.

```php
use Syntro\SilverShare\Interfaces\SharingMetaSource;

class Region extends DataObject implements SharingMetaSource
{
    // define necessary methods
}
```
Now you can tell your page controller to use the DataObject as a source:

```php
class RegionController extends PageController
{
    // ...
    //
    public function show(HTTPRequest $request)
    {
        $region = Region::get()->byID($request->param('ID'));
        $this->setSharingSource($region); // <- This is all you need here
        return [
            'Region' => $region
        ]
    }
}
```

## Default Information
The information used when sharing an object does not always have to be manually
entered. in most cases, there is some image, title and summary available from a
record. You can configure the default fallback field for each value by adding
this to the configuration:
```yaml
SilverStripe\CMS\Model\SiteTree:
  sharing_fallback_image: Image
  sharing_fallback_title: Title
  sharing_fallback_description: MetaDescription
```
> **Note**: Currently, the module only supports fields, but we plan to extend
> this to also include functions and even relations.

## Render Types
By default, only these types are able to be chosen by the editor:
```yaml
SilverStripe\CMS\Model\SiteTree:
  sharing_available_og_types:
    - website
    - article
  sharing_available_twitter_types:
    - summary
    - summary_large_image
```
if you need more types to be dynamically chosen, you can overwrite the config to
add more. You can translate them by adding
```yaml
en:
  Syntro\SilverShare\Extension\ShareExtension:
    website: Webseite
```
to your translation files.
