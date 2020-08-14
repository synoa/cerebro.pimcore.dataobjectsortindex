<h1>Index Sort Bundle</h1>

> Update the "sorting" attribute for children of objects that are sortet "alphabetical"

> Manual sorting is not covered by this extension, you have to do this yourself!

- [Setup](#setup)
  - [Install](#install)
  - [Uninstall](#uninstall)
  - [Add the sorting attribute to the Class](#add-the-sorting-attribute-to-the-class)
- [Configuration](#configuration)
  - [Example](#example)
  - [Example: Multiple folders of the same class](#example-multiple-folders-of-the-same-class)
  - [Example: Different classes](#example-different-classes)
- [Usage](#usage)
  - [Alphabetical Sorting](#alphabetical-sorting)
    - [CLI](#cli)
  - [Manual Sorting](#manual-sorting)
- [FAQ](#faq)
  - [Why is the sorting not updated?](#why-is-the-sorting-not-updated)
    - [Is the sorting type set to `Aplphabetical sorting` in the Pimcore backend?](#is-the-sorting-type-set-to-aplphabetical-sorting-in-the-pimcore-backend)
    - [Did you change the `o_key`?](#did-you-change-the-o_key)
    - [Did you refresh the object?](#did-you-refresh-the-object)
    - [Is the `pimcore:maintenance` actually running?](#is-the-pimcoremaintenance-actually-running)
    - [Does the sorting attribute actually exist at the class?](#does-the-sorting-attribute-actually-exist-at-the-class)

---

# Setup

There are 4 steps in order to get this working

1. [Install the extension](#install)
2. [Add the sorting attribute to the object that should be sorted](#add-the-sorting-attribute-to-the-class)
3. [Configure the folders that should be sorted](#configuration)
4. [Make sure that the default pimcore cron is running or trigger it manually!](#usage)


## Install

Install with composer:

```
# Install module
composer config repositories.synoa_dataobjectsortindex git https://github.com/synoa/cerebro.pimcore.dataobjectsortindex.git
COMPOSER_MEMORY_LIMIT=-1 composer require synoa/apidataobjectsort

# Enable the extension and add the updated var/config/extensions.php into the repo
bin/console pimcore:bundle:enable DataObjectSortIndexBundle
```


## Uninstall

```
COMPOSER_MEMORY_LIMIT=-1 composer remove synoa/apidataobjectsort
composer config unset repositories.synoa_dataobjectsortindex
```

---

## Add the sorting attribute to the Class

In order to save the sorting value into an attribute that can be used by the API, you need to add the attribute to the object. 

* Settings > Data Objects > Classes
* Create the attribute named `sorting` of type `Number`
* Save the class

---

# Configuration

Add the following to a symfony config (eg. `app/config/config.yml`):


```yaml
data_object_sort_index:
    sort_index:
        products:
            folder: /full/path/to/the/parent-object
            object_class: <type of the object that should be sorted, e.g. product or category>
            data_object_field: <name of the field that should be used to save the sorting>
            type: alphabetic
            recursive: <boolean value that tells the extension to also update the sorting of every child>
```

## Example

In Pimcore you have a structure like this:

![Pimcore Objects that should be sorted](docs/images/pimcore_objects_to_sort.png)

Now you want that all sub-objects in `/Products/Website2 (pimadofashion)/Store1 (pimado fashion euro)/Shoes` with the type `product` are sorted by their name (`o_key` in Pimcore, not the actual field `Name` of the Object) using the field `sorting` to store the value: 

```yaml
data_object_sort_index:
    sort_index:
        products:
            folder: /Products/Website2 (pimadofashion)/Store1 (pimado fashion euro)/Shoes
            object_class: product
            data_object_field: sorting
            type: alphabetic
            recursive: true

```

## Example: Multiple folders of the same class

If you want to have multiple folders sorted, you can add them like this:

```yaml
data_object_sort_index:
    sort_index:
        products:
          - folder: /Products/Website2 (pimadofashion)/Store1 (pimado fashion euro)/Shoes
            object_class: product
            data_object_field: sorting
            type: alphabetic
            recursive: true
          - folder: /Products/Website2 (pimadofashion)/Store1 (pimado fashion euro)/Shirts
            object_class: product
            data_object_field: sorting
            type: alphabetic
            recursive: true

```

## Example: Different classes

```yaml
data_object_sort_index:
    sort_index:
        products:
            folder: /Products/Website2 (pimadofashion)/Store1 (pimado fashion euro)/Shoes
            object_class: product
            data_object_field: sorting
            type: alphabetic
            recursive: true
        categories:
            folder: /Shops/Website1 (konekti showcase)/Store1 (konekti showcase store)/Smart Home
            object_class: category
            data_object_field: sorting
            type: alphabetic
            recursive: true
```


---

# Usage

## Alphabetical Sorting

Right click on the object that you want to sort and select `Sort children by > Key (Alphabetically)`.

After the different paths are added via the config, the sorting happens automatially when the `pimcore:maintenance` job (usually triggered via the default Pimcore cron) is executed, which means that you should see your result after ~ 5 minutes. 

### CLI

You can also trigger the sorting manually: `bin/console synoa:data_object_sort_index:sort`

## Manual Sorting

If you want to sort your objects manually, you have to do some things:

* Don't add your folder to the configuration-file as written above! This is ONLY for folders that use alphabetical sorting!
* Right click on the object that you want to sort manually and select `Sort children by > Index (Ordered by Manually)`
* Order your objects manually
* Update the `sorting` attribute yourself every time the objects are sorted


---

# FAQ


## Why is the sorting not updated?

### Is the sorting type set to `Aplphabetical sorting` in the Pimcore backend?

The extension only works if the sorting type is set to `alphabetic sorting`. To change this, you have to right click the folder you want to sort and select `Sort children by > Key (Alphabetically)`.

### Did you change the `o_key`? 

This is the name of the object in the list, NOT the field `Name` of the object when you edit the object.

You can change the `o_key` with right click on the object and in the context menu choose `rename`.

---

### Did you refresh the object? 

As when you had the object open before, refreshing the object-tree doesn't refresh the indiviual object

![Sorting is not updated because you have to refresh the object](docs/images/pimcore_objects_refresh_object.jpg)

---

### Is the `pimcore:maintenance` actually running?

Go onto the server and open the list of cronjobs with `crontab -l` and see if this is added:

```
*/5 * * * * ~/pimcore/current/bin/console pimcore:maintenance
```

If not then it needs to be added. 

---

### Does the sorting attribute actually exist at the class?

Please make sure that the attribute is added to the class that you want to have sorted. 