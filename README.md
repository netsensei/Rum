README
======

Author
------

Matthias "Netsensei" Vandermaesen
matthias@colada.be
http://www.colada.be

What is Rum?
------------

Rum is a set of Drush scripts that makes a Drupal developers' life easier.
It allows you to quickly set up and tear down Drupal websites with as few
commands as possible. Rum takes away the pain of tediously managing virtual
hosts, databases,... 

The aim is to keep your development setup as clean as a whistle and allowing you
to set up a new Drupal site quickly.

Why Rum?
--------

Although a myriad of general solutions already exists, the idea behind Rum is to 
do one thing only: quickly setting up and tearing down Drupal websites.

Rum is also a learning experience. I wanted to learn more about Drush and I wanted
to really dive into OOP and design patterns.

WARNING: INSTALL AT YOUR OWN RISK! THIS IS STILL AN UNFINISHED PROJECT. Rum will
interact (add/remove) with your installation, so things might break severely.

Features
--------

* Manage your Drupal instances (Setup a new virtual host, a database and a host name)
* Set up Drupal vanilla or from an installation profile.
* Install a project from a CVS (Git, SVN)
* Platform independent (MAMP, Ubuntu,...)

Installation
------------

1. Put the rum file base in a your /home/<user>/.drush folder
2. Configure your .drushrc file with the rum specific options. Rum will tell
   you when its' missing a crucial configuration parameter

Usage
-----

Install a vanilla Drupal project called foobar
~ drush rc vanilla foobar

Install a project Foobar from a CVS repository (local or remote)
~ drush rc repository foobar

Both commands will create a domain name called hostname.foobar. Navigate to
http://hostname.foobar to see your project.

Project source will be found in a workspace directory in your home directory.

Remove a project (vhost, folders, link, database,...):
~ sudo drush rd foobar

Beware: sudo is required to make this work correctly!

Roadmap
-------

Rum relies on the Drush API. There are few things on the wishlist

* Each drush_COMMAND has an init, validate, pre, command and post phase. Rum doesn't
  use this although it might benefit heavily from doing so.
* Integrate better with Drush native error handling through exceptions since this
  opens up the opportunity to use drush' rollback hooks.
