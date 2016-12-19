Symfony Sonata Configuration Panel
============
This bundle adds configuration panel to your sonata admin.

This bundle uses [IvoryCKEditorBundle](https://github.com/egeloen/IvoryCKEditorBundle) , be sure to check it out.

This bundle depends on [SonataAdminBundle](https://github.com/sonata-project/SonataAdminBundle))

![Dashboard](https://cloud.githubusercontent.com/assets/13528674/21304601/a1c4936e-c5c6-11e6-9834-2d9f9942c5ff.png)
Documentation
-------------


* [Installation](#installation)
  * [FileType](#filetype)
  * [MediaType](#mediatype)
* [How to use](#how-to-use)
* [Roles and Categories](#roles-and-categories)
* [Checkbox and Choice Type](#checkbox-and-choice-type)
* [Additional stuff](#additional-stuff)


## Installation

**1.**  Add to composer.json to the `require` key

```
composer require kunicmarko/configuration-panel
```

**2.** Register the bundle in ``app/AppKernel.php``

```
$bundles = array(
    // ...
    new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
    new KunicMarko\ConfigurationPanelBundle\ConfigurationPanelBundle(),
);
```

**3.** Add configuration
```
# app/config/config.yml

configuration_panel
    type: YourBundle\Entity\FileType #or YourBundle\Entity\MediaType
    upload_directory: uploads #directory in web folder where you want to upload stuff, if you are using Sonata Media, this is not needed
```

**4.** Creating new Type

If you are using Sonata Media and want to use it with configuration panel, then you need to create MediaType, if you don't want to use it then create FileType

## FileType

```
<?php

namespace YourBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use KunicMarko\ConfigurationPanelBundle\Entity\Configuration;
use KunicMarko\ConfigurationPanelBundle\Traits\TemplateTrait;
use KunicMarko\ConfigurationPanelBundle\Entity\ConfigurationTypes\TemplateInterface;

 /**
 *
 * @ORM\Entity
 *
 */
class FileType extends Configuration implements TemplateInterface
{
    use TemplateTrait;
    private static $template = 'ConfigurationPanelBundle:CRUD:list_field_file.html.twig';
    /*
     * Saves old value for removing or update without file.
     *
     * @var string
     */
    private $oldFile;

    public function setValue($value)
    {
        if($this->value !== null){
            $this->oldFile = $this->value;
        }
        $this->value = $value;
        return $this;
    }

    public function getOldFile(){
        return $this->oldFile;
    }

}

```

## MediaType

```
<?php

namespace YourBundle\Entity;
use Doctrine\ORM\Mapping as ORM;
use KunicMarko\ConfigurationPanelBundle\Entity\Configuration;
use KunicMarko\ConfigurationPanelBundle\Traits\TemplateTrait;
use KunicMarko\ConfigurationPanelBundle\Entity\ConfigurationTypes\TemplateInterface;

 /**
 *
 * @ORM\Entity
 *
 */
class MediaType extends Configuration implements TemplateInterface
{
    use TemplateTrait;
    private static $template = 'ConfigurationPanelBundle:CRUD:list_field_media.html.twig';
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media", cascade={"persist"})
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="media", referencedColumnName="id", onDelete="SET NULL")
     * })
     */
    private $media;

    /**
     * Set media
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $media
     *
     * @return MediaType
     */
    public function setMedia($media)
    {
        $this->media = $media;
    }

    /**
     * Get media
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getMedia()
    {
        return $this->media;
    }

    public function hydrateMediaObject()
    {
        return $this->media;
    }
}

```
Also if using SonataMedia you have to setup context for configuration panel :

```
# app/config/config.yml
sonata_media:
    #...
    contexts:
        default:  # the default context is mandatory
            providers:
                - sonata.media.provider.dailymotion
                - sonata.media.provider.youtube
                - sonata.media.provider.image
                - sonata.media.provider.file
                - sonata.media.provider.vimeo

            formats:
                small: { width: 100 , quality: 70}
                big:   { width: 500 , quality: 70}

        configuration_panel:  #setup how you want
            providers:
                - sonata.media.provider.image
                - sonata.media.provider.file

            formats:
                small: { width: 100 , quality: 70}
                big:   { width: 500 , quality: 70}

```
**5.** Update database

```
app\console doctrine:schema:update --force
```

**6.** Install Assets

```
app\console assets:install
```

If upload folder is not already created:
```
mkdir web/uploads
chmod -R 0777 web/uploads
```

## How to use

In your twig template you can call it like :
```
{{ configuration.getAll() }}
{{ configuration.getValueFor(name) }}
```

if you want to use it in controller you can do :
```
$this->get('global.configuration')->getAll()
$this->get('global.configuration')->getValueFor()
```

## Roles and Categories

This bundle was made as help to developers so only `ROLE_SUPER_ADMIN` can create and delete items, regular admins can only edit. ( You can create keys and allow other admins to just edit them ).
There are 2 categories when creating item, `Meta` and `General`, only `ROLE_SUPER_ADMIN` can see and edit items that are in `META` category while normal admins can only edit and see `General` items.

## Checkbox and Choice Type

![Checkbox](https://cloud.githubusercontent.com/assets/13528674/21304614/c08d54de-c5c6-11e6-91e9-d6df5ced7cec.png)

As you can see there is Options value that is only visible to `ROLE_SUPER_ADMIN`, correct format is :
```
value:Label
value2:label2
value3:label3
```

`( Separator is enter key )`

## Additional stuff

With using this bundle you get access to some twig filters I needed.

**1.** Elapsed

In twig you can use `|elapsed` filter and you will get human readable time, it works with timestamps or DateTime objects.
```
{{ var|elapsed }}

#outputs "time" ago, 5 days ago, 5 minutes ago, just now, 1 month ago, etc.
```

**2.** GenerateURL

If you are not using sonataMedia, you can use `|generateURL` to get absolute url to your image
```
<img src="{{ var|generateURL }}" />
```

**3.** isImage

You can also use `|isImage` to check if file is image
```
{{ var|isImage }}
#outputs true or false
```

Also this bundle depends on [IvoryCKEditorBundle](https://github.com/egeloen/IvoryCKEditorBundle) as we said on beginning so you can use ckeditor as formtype :

```
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

$builder->add('value', CKEditorType::class);
```
